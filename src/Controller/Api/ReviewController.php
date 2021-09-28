<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Exception\NotFoundException;
use App\Model\Review\SubmitLocaleReviewInput;
use App\Service\GoogleReCaptchaService;
use App\Service\ReviewService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class ReviewController extends AppController
{
    use VerifyCaptchaTrait;

    public function __construct(
        private GoogleReCaptchaService $googleReCaptchaService,
        private ReviewService $reviewService,
        protected SerializerInterface $serializer,
    ) {
    }

    /**
     * @Route (
     *     "/api/review/locale",
     *     name="submit-locale-review",
     *     methods={"POST"}
     * )
     */
    public function submitLocaleReview(Request $request): JsonResponse
    {
        try {
            /** @var SubmitLocaleReviewInput $input */
            $input = $this->serializer->deserialize($request->getContent(), SubmitLocaleReviewInput::class, 'json');
        } catch (Exception $e) {
            $this->addDeserializationFailedFlashMessage();

            return $this->jsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        if (!$this->verifyCaptcha($input->getCaptchaToken(), $request)) {
            $this->addFlash('error', 'Sorry, we were unable to process your review.');

            return $this->jsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        try {
            $output = $this->reviewService->submitLocaleReview($input, $this->getUserInterface());
        } catch (NotFoundException $e) {
            return $this->jsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $this->addFlash(
            'success',
            'Your review was received successfully and will be checked by our moderation team shortly.'
        );

        return $this->jsonResponse($output, Response::HTTP_CREATED);
    }
}
