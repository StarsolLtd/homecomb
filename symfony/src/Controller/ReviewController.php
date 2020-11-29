<?php

namespace App\Controller;

use App\Model\SubmitReviewInput;
use App\Repository\ReviewRepository;
use App\Service\GoogleReCaptchaService;
use App\Service\ReviewService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ReviewController extends AbstractController
{
    private GoogleReCaptchaService $googleReCaptchaService;
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

        if (!$this->verifyReCaptcha($input->getGoogleReCaptchaToken(), $request)) {
            $this->addFlash('error', 'Sorry, we were unable to process your review.');

            return JsonResponse::create([], Response::HTTP_BAD_REQUEST);
        }

        $output = $this->reviewService->submitReview($input);

        $this->addFlash(
            'notice',
            'Your review was received successfully and will be checked by our moderation team shortly.'
        );

        return JsonResponse::create(
            [
                'id' => $output->getId(),
            ],
            Response::HTTP_CREATED
        );
    }

    private function verifyReCaptcha(?string $token, Request $request): bool
    {
        return $this->googleReCaptchaService->verify($token, $request->getClientIp(), $request->getHost());
    }
}
