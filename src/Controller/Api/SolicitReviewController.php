<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Exception\NotFoundException;
use App\Model\TenancyReviewSolicitation\CreateReviewSolicitationInput;
use App\Service\Branch\BranchAdminService;
use App\Service\GoogleReCaptchaService;
use App\Service\TenancyReviewSolicitation\CreateService as TenancyReviewSolicitationCreateService;
use App\Service\TenancyReviewSolicitation\GetFormDataService as TenancyReviewSolicitationGetFormService;
use App\Service\User\UserService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;

final class SolicitReviewController extends AppController
{
    use VerifyCaptchaTrait;

    public function __construct(
        private BranchAdminService $branchAdminService,
        private GoogleReCaptchaService $googleReCaptchaService,
        private TenancyReviewSolicitationGetFormService $tenancyReviewSolicitationGetFormDataService,
        private TenancyReviewSolicitationCreateService $tenancyReviewSolicitationCreateService,
        private UserService $userService,
        protected SerializerInterface $serializer,
    ) {
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

        $output = $this->tenancyReviewSolicitationGetFormDataService->getFormData($this->getUserInterface());

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

        try {
            /** @var CreateReviewSolicitationInput $input */
            $input = $this->serializer->deserialize($request->getContent(), CreateReviewSolicitationInput::class, 'json');
        } catch (Exception $e) {
            $this->addDeserializationFailedFlashMessage();

            return $this->jsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        if (!$this->verifyCaptcha($input->getCaptchaToken(), $request)) {
            $this->addFlash('error', 'Sorry, we were unable to process your review solicitation.');

            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->getUserInterface();
        if (!$this->branchAdminService->isUserBranchAdmin($input->getBranchSlug(), $user)) {
            $this->addFlash('error', 'Sorry, you are not logged in as an admin for this agency.');

            return new JsonResponse([], Response::HTTP_FORBIDDEN);
        }

        try {
            $output = $this->tenancyReviewSolicitationCreateService->createAndSend($input, $user);
        } catch (NotFoundException $e) {
            return $this->jsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $this->addFlash(
            'success',
            'An email will be sent to '.$input->getRecipientEmail().' shortly asking them to review their tenancy.'
        );

        return $this->jsonResponse($output, Response::HTTP_CREATED);
    }
}
