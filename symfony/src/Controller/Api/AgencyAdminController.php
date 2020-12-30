<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Exception\ConflictException;
use App\Exception\NotFoundException;
use App\Model\Agency\CreateAgencyInput;
use App\Model\Agency\UpdateAgencyInput;
use App\Model\Branch\CreateBranchInput;
use App\Model\Branch\UpdateBranchInput;
use App\Model\ReviewSolicitation\CreateReviewSolicitationInput;
use App\Repository\AgencyRepository;
use App\Service\AgencyAdminService;
use App\Service\AgencyService;
use App\Service\BranchService;
use App\Service\GoogleReCaptchaService;
use App\Service\ReviewSolicitationService;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;

class AgencyAdminController extends AppController
{
    use VerifyCaptchaTrait;

    private AgencyAdminService $agencyAdminService;
    private AgencyService $agencyService;
    private BranchService $branchService;
    private ReviewSolicitationService $reviewSolicitationService;
    private UserService $userService;
    private AgencyRepository $agencyRepository;

    public function __construct(
        AgencyAdminService $agencyAdminService,
        AgencyService $agencyService,
        BranchService $branchService,
        GoogleReCaptchaService $googleReCaptchaService,
        ReviewSolicitationService $reviewSolicitationService,
        UserService $userService,
        AgencyRepository $agencyRepository,
        SerializerInterface $serializer
    ) {
        $this->agencyAdminService = $agencyAdminService;
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
        } catch (AccessDeniedException $e) {
            throw new AccessDeniedHttpException($e->getMessage());
        }

        /** @var CreateAgencyInput $input */
        $input = $this->serializer->deserialize($request->getContent(), CreateAgencyInput::class, 'json');

        if (!$this->verifyCaptcha($input->getCaptchaToken(), $request)) {
            $this->addFlash('error', 'Sorry, we were unable to process your agency creation.');

            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        try {
            $output = $this->agencyService->createAgency($input, $this->getUserInterface());
        } catch (ConflictException $e) {
            $this->addFlash('error', 'Sorry, we were unable to process your agency as you are already an agency admin.');
            throw new ConflictHttpException($e->getMessage());
        }

        $this->addFlash(
            'notice',
            'Your agency was created successfully and will be reviewed by our moderation team before being '
            .'published shortly. You can now add branches, upload a logo etc.'
        );

        return $this->jsonResponse($output, Response::HTTP_CREATED);
    }

    /**
     * @Route (
     *     "/api/verified/agency",
     *     name="get-agency",
     *     methods={"GET"}
     * )
     */
    public function getAgencyForUser(): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted('ROLE_USER');
        } catch (AccessDeniedException $e) {
            throw new AccessDeniedHttpException($e->getMessage());
        }

        $output = $this->agencyService->getAgencyForUser($this->getUserInterface());

        return $this->jsonResponse($output, Response::HTTP_OK);
    }

    /**
     * @Route (
     *     "/api/verified/agency/{slug}",
     *     name="update-agency",
     *     methods={"PUT"}
     * )
     */
    public function updateAgency(string $slug, Request $request): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted('ROLE_USER');
        } catch (AccessDeniedException $e) {
            throw new AccessDeniedHttpException($e->getMessage());
        }

        /** @var UpdateAgencyInput $input */
        $input = $this->serializer->deserialize($request->getContent(), UpdateAgencyInput::class, 'json');

        if (!$this->verifyCaptcha($input->getCaptchaToken(), $request)) {
            $this->addFlash('error', 'Sorry, we were unable to process your agency update.');

            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $output = $this->agencyService->updateAgency($slug, $input, $this->getUserInterface());

        $this->addFlash(
            'notice',
            'Your agency was updated successfully.'
        );

        return $this->jsonResponse($output, Response::HTTP_OK);
    }

    /**
     * @Route (
     *     "/api/verified/branch/{slug}",
     *     name="api-verified-branch-get",
     *     methods={"GET", "HEAD"}
     * )
     */
    public function getBranch(string $slug): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted('ROLE_USER');
        } catch (AccessDeniedException $e) {
            throw new AccessDeniedHttpException($e->getMessage());
        }

        try {
            $output = $this->agencyAdminService->getBranch($slug, $this->getUserInterface());
        } catch (NotFoundException $e) {
            return $this->jsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        return $this->jsonResponse($output, Response::HTTP_OK);
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
        } catch (AccessDeniedException $e) {
            throw new AccessDeniedHttpException($e->getMessage());
        }

        /** @var CreateBranchInput $input */
        $input = $this->serializer->deserialize($request->getContent(), CreateBranchInput::class, 'json');

        if (!$this->verifyCaptcha($input->getCaptchaToken(), $request)) {
            $this->addFlash('error', 'Sorry, we were unable to process your branch creation.');

            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $output = $this->branchService->createBranch($input, $this->getUserInterface());

        $this->addFlash(
            'notice',
            'Your new branch was created successfully.'
        );

        return $this->jsonResponse($output, Response::HTTP_CREATED);
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
        } catch (AccessDeniedException $e) {
            throw new AccessDeniedHttpException($e->getMessage());
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

        return $this->jsonResponse($output, Response::HTTP_OK);
    }

    /**
     * @Route (
     *     "/api/verified/solicit-review",
     *     name="solicit-review-form-data",
     *     methods={"GET"}
     * )
     */
    public function solicitReviewFormData(): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted('ROLE_USER');
        } catch (AccessDeniedException $e) {
            throw new AccessDeniedHttpException($e->getMessage());
        }

        $output = $this->reviewSolicitationService->getFormData($this->getUserInterface());

        return $this->jsonResponse($output, Response::HTTP_OK);
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
        } catch (AccessDeniedException $e) {
            throw new AccessDeniedHttpException($e->getMessage());
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

        try {
            $output = $this->reviewSolicitationService->createAndSend($input, $user);
        } catch (NotFoundException $e) {
            return $this->jsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $this->addFlash(
            'notice',
            'An email will be sent to '.$input->getRecipientEmail().' shortly asking them to review their tenancy.'
        );

        return $this->jsonResponse($output, Response::HTTP_CREATED);
    }

    /**
     * @Route (
     *     "/api/verified/agency-admin",
     *     name="api-agency-admin",
     *     methods={"GET"}
     * )
     */
    public function home(): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted('ROLE_USER');
        } catch (AccessDeniedException $e) {
            throw new AccessDeniedHttpException($e->getMessage());
        }

        try {
            $output = $this->agencyAdminService->getHomeForUser($this->getUserInterface());
        } catch (NotFoundException $e) {
            return $this->jsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        return $this->jsonResponse($output, Response::HTTP_OK);
    }
}
