<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Exception\NotFoundException;
use App\Factory\InteractionFactory;
use App\Model\TenancyReview\SubmitInput;
use App\Repository\TenancyReviewRepository;
use App\Service\GoogleReCaptchaService;
use App\Service\TenancyReviewService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class TenancyReviewController extends AppController
{
    use VerifyCaptchaTrait;

    private TenancyReviewRepository $tenancyReviewRepository;
    private TenancyReviewService $tenancyReviewService;

    public function __construct(
        GoogleReCaptchaService $googleReCaptchaService,
        TenancyReviewService $tenancyReviewService,
        InteractionFactory $interactionFactory,
        SerializerInterface $serializer
    ) {
        $this->googleReCaptchaService = $googleReCaptchaService;
        $this->tenancyReviewService = $tenancyReviewService;
        $this->interactionFactory = $interactionFactory;
        $this->serializer = $serializer;
    }

    /**
     * @Route (
     *     "/api/submit-review",
     *     name="submit-review",
     *     methods={"POST"}
     * )
     */
    public function submitReview(Request $request): JsonResponse
    {
        try {
            /** @var SubmitInput $input */
            $input = $this->serializer->deserialize($request->getContent(), SubmitInput::class, 'json');
        } catch (Exception $e) {
            $this->addDeserializationFailedFlashMessage();

            return $this->jsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        if (!$this->verifyCaptcha($input->getCaptchaToken(), $request)) {
            $this->addFlash('error', 'Sorry, we were unable to process your review.');

            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $output = $this->tenancyReviewService->submitReview(
            $input,
            $this->getUserInterface(),
            $this->getRequestDetails($request)
        );

        return $this->jsonResponse($output, Response::HTTP_CREATED);
    }

    /**
     * @Route (
     *     "/api/review/{id}",
     *     requirements={"id"="\d+"},
     *     name="api-review-id",
     *     methods={"GET"}
     * )
     */
    public function view(int $id): JsonResponse
    {
        try {
            $view = $this->tenancyReviewService->getViewById($id);
        } catch (NotFoundException $e) {
            return $this->jsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        return $this->jsonResponse($view, Response::HTTP_OK);
    }

    /**
     * @Route (
     *     "/api/review/latest",
     *     name="api-review-latest",
     *     methods={"GET"}
     * )
     */
    public function latest(): JsonResponse
    {
        $latest = $this->tenancyReviewService->getLatestGroup();

        return $this->jsonResponse($latest, Response::HTTP_OK);
    }
}
