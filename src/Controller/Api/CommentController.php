<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Entity\TenancyReview;
use App\Exception\NotFoundException;
use App\Exception\UnexpectedValueException;
use App\Model\Comment\SubmitInput;
use App\Service\CommentService;
use App\Service\GoogleReCaptchaService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class CommentController extends AppController
{
    use VerifyCaptchaTrait;

    public function __construct(
        private GoogleReCaptchaService $googleReCaptchaService,
        private CommentService $commentService,
        protected SerializerInterface $serializer,
    ) {
    }

    /**
     * @Route (
     *     "/api/comment",
     *     name="comment",
     *     methods={"POST"}
     * )
     */
    public function submitComment(Request $request): JsonResponse
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

            return $this->jsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        if (!$this->checkPrivilege('comment', TenancyReview::class, $input->getEntityId())) {
            return $this->jsonResponse(null, Response::HTTP_FORBIDDEN);
        }

        try {
            $output = $this->commentService->submitComment($input, $this->getUserInterface());
        } catch (UnexpectedValueException $e) {
            return $this->jsonResponse(null, Response::HTTP_BAD_REQUEST);
        } catch (NotFoundException $e) {
            return $this->jsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $this->addFlash(
            'success',
            'Your comment was received successfully and will be checked by our moderation team shortly.'
        );

        return $this->jsonResponse($output, Response::HTTP_CREATED);
    }
}
