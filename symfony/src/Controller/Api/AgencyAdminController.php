<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Model\Agency\CreateAgencyInput;
use App\Model\Branch\CreateBranchInput;
use App\Model\Branch\UpdateBranchInput;
use App\Model\ReviewSolicitation\CreateReviewSolicitationInput;
use App\Repository\AgencyRepository;
use App\Service\AgencyService;
use App\Service\BranchService;
use App\Service\GoogleReCaptchaService;
use App\Service\ReviewSolicitationService;
use App\Service\UserService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AgencyAdminController extends AppController
{
    private AgencyService $agencyService;
    private BranchService $branchService;
    private GoogleReCaptchaService $googleReCaptchaService;
    private ReviewSolicitationService $reviewSolicitationService;
    private UserService $userService;
    private AgencyRepository $agencyRepository;
    private SerializerInterface $serializer;

    public function __construct(
        AgencyService $agencyService,
        BranchService $branchService,
        GoogleReCaptchaService $googleReCaptchaService,
        ReviewSolicitationService $reviewSolicitationService,
        UserService $userService,
        AgencyRepository $agencyRepository,
        SerializerInterface $serializer
    ) {
        $this->agencyService = $agencyService;
        $this->branchService = $branchService;
        $this->googleReCaptchaService = $googleReCaptchaService;
        $this->reviewSolicitationService = $reviewSolicitationService;
        $this->userService = $userService;
        $this->agencyRepository = $agencyRepository;
        $this->serializer = $serializer;
    }

    /**
     * @Route (
     *     "/api/verified/agency",
     *     name="create-agency",
     *     methods={"POST"}
     * )
     */
    public function createAgency(Request $request): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted('ROLE_USER');
        } catch (Exception $e) {
            return new JsonResponse(
                [
                    'success' => false,
                ],
                Response::HTTP_FORBIDDEN
            );
        }

        /** @var CreateAgencyInput $input */
        $input = $this->serializer->deserialize($request->getContent(), CreateAgencyInput::class, 'json');

        if (!$this->verifyCaptcha($input->getGoogleReCaptchaToken(), $request)) {
            $this->addFlash('error', 'Sorry, we were unable to process your agency creation.');

            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $output = $this->agencyService->createAgency($input, $this->getUserInterface());

        $this->addFlash(
            'notice',
            'Your agency was created successfully and will be reviewed by our moderation team before being '
            .'published shortly. You can now add branches, upload a logo etc.'
        );

        return new JsonResponse(
            [
                'success' => $output->isSuccess(),
            ],
            Response::HTTP_CREATED
        );
    }

    /**
     * @Route (
     *     "/api/verified/branch",
     *     name="create-branch",
     *     methods={"POST"}
     * )
     */
    public function createBranch(Request $request): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted('ROLE_USER');
        } catch (Exception $e) {
            return new JsonResponse(
                [
                    'success' => false,
                ],
                Response::HTTP_FORBIDDEN
            );
        }

        /** @var CreateBranchInput $input */
        $input = $this->serializer->deserialize($request->getContent(), CreateBranchInput::class, 'json');

        if (!$this->verifyCaptcha($input->getGoogleReCaptchaToken(), $request)) {
            $this->addFlash('error', 'Sorry, we were unable to process your branch creation.');

            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $output = $this->branchService->createBranch($input, $this->getUserInterface());

        $this->addFlash(
            'notice',
            'Your new branch was created successfully.'
        );

        return new JsonResponse(
            [
                'success' => $output->isSuccess(),
            ],
            Response::HTTP_CREATED
        );
    }

    /**
     * @Route (
     *     "/api/verified/branch/{slug}",
     *     name="update-branch",
     *     methods={"PUT"}
     * )
     */
    public function updateBranch(string $slug, Request $request): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted('ROLE_USER');
        } catch (Exception $e) {
            return new JsonResponse(
                [
                    'success' => false,
                ],
                Response::HTTP_FORBIDDEN
            );
        }

        /** @var UpdateBranchInput $input */
        $input = $this->serializer->deserialize($request->getContent(), UpdateBranchInput::class, 'json');

        if (!$this->verifyCaptcha($input->getCaptchaToken(), $request)) {
            $this->addFlash('error', 'Sorry, we were unable to process your branch creation.');

            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $output = $this->branchService->updateBranch($slug, $input, $this->getUserInterface());

        $this->addFlash(
            'notice',
            'Your branch was updated successfully.'
        );

        return new JsonResponse(
            [
                'success' => $output->isSuccess(),
            ],
            Response::HTTP_OK
        );
    }

    /**
     * @Route (
     *     "/api/verified/solicit-review",
     *     name="solicit-review",
     *     methods={"POST"}
     * )
     */
    public function solicitReview(Request $request): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted('ROLE_USER');
        } catch (Exception $e) {
            return new JsonResponse(
                [
                    'success' => false,
                ],
                Response::HTTP_FORBIDDEN
            );
        }

        /** @var CreateReviewSolicitationInput $input */
        $input = $this->serializer->deserialize($request->getContent(), CreateReviewSolicitationInput::class, 'json');

        if (!$this->verifyCaptcha($input->getCaptchaToken(), $request)) {
            $this->addFlash('error', 'Sorry, we were unable to process your review solicitation.');

            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->getUserInterface();
        if (!$this->userService->isUserBranchAdmin($input->getBranchSlug(), $user)) {
            $this->addFlash('error', 'Sorry, you are not logged in as an admin for this agency.');

            return new JsonResponse([], Response::HTTP_FORBIDDEN);
        }

        $output = $this->reviewSolicitationService->createAndSend($input, $user);

        $this->addFlash(
            'notice',
            'An email will be sent to '.$input->getRecipientEmail().' shortly asking them to review their tenancy.'
        );

        return new JsonResponse(
            [
                'success' => $output->isSuccess(),
            ],
            Response::HTTP_CREATED
        );
    }

    private function verifyCaptcha(?string $token, Request $request): bool
    {
        return $this->googleReCaptchaService->verify($token, $request->getClientIp(), $request->getHost());
    }
}
