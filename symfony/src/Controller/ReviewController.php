<?php

namespace App\Controller;

use App\Model\SubmitReviewInput;
use App\Repository\ReviewRepository;
use App\Service\ReviewService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ReviewController extends AbstractController
{
    private ReviewRepository $reviewRepository;
    private ReviewService $reviewService;
    private SerializerInterface $serializer;

    public function __construct(
        ReviewRepository $reviewRepository,
        ReviewService $reviewService,
        SerializerInterface $serializer
    ) {
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
}
