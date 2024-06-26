<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Exception\ConflictException;
use App\Exception\NotFoundException;
use App\Model\Agency\CreateInput;
use App\Model\Agency\UpdateInput;
use App\Model\Branch\CreateInput as BranchCreateInput;
use App\Model\Branch\UpdateInput as BranchUpdateInput;
use App\Service\Agency\CreateService as AgencyCreateService;
use App\Service\Agency\UpdateService as AgencyUpdateService;
use App\Service\AgencyAdminService;
use App\Service\Branch\BranchAdminService;
use App\Service\Branch\CreateService as BranchCreateService;
use App\Service\Branch\UpdateService as BranchUpdateService;
use App\Service\GoogleReCaptchaService;
use App\Service\User\GetAgencyService as UserGetAgencyService;
use App\Service\User\UserService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;

final class AgencyAdminController extends AppController
{
    use VerifyCaptchaTrait;

    public function __construct(
        private AgencyAdminService $agencyAdminService,
        private UserGetAgencyService $userGetAgencyService,
        private AgencyCreateService $agencyCreateService,
        private AgencyUpdateService $agencyUpdateService,
        private BranchAdminService $branchAdminService,
        private BranchCreateService $branchCreateService,
        private BranchUpdateService $branchUpdateService,
        private GoogleReCaptchaService $googleReCaptchaService,
        private UserService $userService,
        protected SerializerInterface $serializer,
    ) {
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

        try {
            /** @var CreateInput $input */
            $input = $this->serializer->deserialize($request->getContent(), CreateInput::class, 'json');
        } catch (Exception $e) {
            $this->addDeserializationFailedFlashMessage();

            return $this->jsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        if (!$this->verifyCaptcha($input->getCaptchaToken(), $request)) {
            $this->addFlash('error', 'Sorry, we were unable to process your agency creation.');

            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        try {
            $output = $this->agencyCreateService->createAgency($input, $this->getUserInterface());
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

        $output = $this->userGetAgencyService->getAgencyForUser($this->getUserInterface());

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

        try {
            /** @var UpdateInput $input */
            $input = $this->serializer->deserialize($request->getContent(), UpdateInput::class, 'json');
        } catch (Exception $e) {
            $this->addDeserializationFailedFlashMessage();

            return $this->jsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        if (!$this->verifyCaptcha($input->getCaptchaToken(), $request)) {
            $this->addFlash('error', 'Sorry, we were unable to process your agency update.');

            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $output = $this->agencyUpdateService->updateAgency($slug, $input, $this->getUserInterface());

        $this->addFlash('success', 'Your agency was updated successfully.');

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

        try {
            /** @var BranchCreateInput $input */
            $input = $this->serializer->deserialize($request->getContent(), BranchCreateInput::class, 'json');
        } catch (Exception $e) {
            $this->addDeserializationFailedFlashMessage();

            return $this->jsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        if (!$this->verifyCaptcha($input->getCaptchaToken(), $request)) {
            $this->addFlash('error', 'Sorry, we were unable to process your branch creation.');

            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        try {
            $output = $this->branchCreateService->createBranch($input, $this->getUserInterface());
        } catch (ConflictException $e) {
            $this->addFlash('warning', $e->getMessage());

            return $this->jsonResponse(null, Response::HTTP_CONFLICT);
        }

        $this->addFlash('success', 'Your new branch, '.$input->getBranchName().', was created successfully.');

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

        try {
            /** @var BranchUpdateInput $input */
            $input = $this->serializer->deserialize($request->getContent(), BranchUpdateInput::class, 'json');
        } catch (Exception $e) {
            $this->addDeserializationFailedFlashMessage();

            return $this->jsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        if (!$this->verifyCaptcha($input->getCaptchaToken(), $request)) {
            $this->addFlash('error', 'Sorry, we were unable to process your branch creation.');

            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $output = $this->branchUpdateService->updateBranch($slug, $input, $this->getUserInterface());

        $this->addFlash('success', 'Your branch was updated successfully.');

        return $this->jsonResponse($output, Response::HTTP_OK);
    }

    /**
     * @Route (
     *     "/api/verified/dashboard",
     *     name="api-verified-dashboard",
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
