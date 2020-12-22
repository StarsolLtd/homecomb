<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Model\SubmitReviewInput;
use App\Repository\ReviewRepository;
use App\Service\GoogleReCaptchaService;
use App\Service\ReviewService;
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
    private SerializerInterface $serializer;

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
        /** @var SubmitReviewInput $input */
        $input = $this->serializer->deserialize($request->getContent(), SubmitReviewInput::class, 'json');

        if (!$this->verifyCaptcha($input->getCaptchaToken(), $request)) {
            $this->addFlash('error', 'Sorry, we were unable to process your review.');

            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $output = $this->reviewService->submitReview($input, $this->getUserInterface());

        $this->addFlash(
            'notice',
            'Your review was received successfully and will be checked by our moderation team shortly.'
        );

        return new JsonResponse(
            [
                'success' => $output->isSuccess(),
            ],
            Response::HTTP_CREATED
        );
    }
}
