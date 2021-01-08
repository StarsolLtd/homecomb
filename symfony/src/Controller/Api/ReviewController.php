<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Exception\NotFoundException;
use App\Model\SubmitReviewInput;
use App\Repository\ReviewRepository;
use App\Service\GoogleReCaptchaService;
use App\Service\ReviewService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ReviewController extends AppController
{
    use VerifyCaptchaTrait;

    private ReviewRepository $reviewRepository;
    private ReviewService $reviewService;

    public function __construct(
        GoogleReCaptchaService $googleReCaptchaService,
        ReviewRepository $reviewRepository,
        ReviewService $reviewService,
        SerializerInterface $serializer
    ) {
        $this->googleReCaptchaService = $googleReCaptchaService;
        $this->reviewRepository = $reviewRepository;
        $this->reviewService = $reviewService;
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
            /** @var SubmitReviewInput $input */
            $input = $this->serializer->deserialize($request->getContent(), SubmitReviewInput::class, 'json');
        } catch (Exception $e) {
            $this->addDeserializationFailedFlashMessage();

            return $this->jsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        if (!$this->verifyCaptcha($input->getCaptchaToken(), $request)) {
            $this->addFlash('error', 'Sorry, we were unable to process your review.');

            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $output = $this->reviewService->submitReview($input, $this->getUserInterface());

        return $this->jsonResponse($output, Response::HTTP_CREATED);
    }

    /**
     * @Route (
     *     "/api/review/{id}",
     *     name="api-review-id",
     *     methods={"GET"}
     * )
     */
    public function view(int $id): JsonResponse
    {
        try {
            $view = $this->reviewService->getViewById($id);
        } catch (NotFoundException $e) {
            return $this->jsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        return $this->jsonResponse($view, Response::HTTP_OK);
    }
}
