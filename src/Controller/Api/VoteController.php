<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Exception\NotFoundException;
use App\Exception\UnexpectedValueException;
use App\Factory\InteractionFactory;
use App\Model\Vote\SubmitInput;
use App\Service\GoogleReCaptchaService;
use App\Service\VoteService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;

class VoteController extends AppController
{
    use VerifyCaptchaTrait;

    public function __construct(
        private GoogleReCaptchaService $googleReCaptchaService,
        private VoteService $voteService,
        protected InteractionFactory $interactionFactory,
        protected SerializerInterface $serializer
    ) {
    }

    /**
     * @Route (
     *     "/api/vote",
     *     name="vote",
     *     methods={"POST"}
     * )
     */
    public function vote(Request $request): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted('ROLE_USER');
        } catch (AccessDeniedException $e) {
            throw new UnauthorizedHttpException($e->getMessage());
        }

        try {
            /** @var SubmitInput $input */
            $input = $this->serializer->deserialize($request->getContent(), SubmitInput::class, 'json');
        } catch (Exception $e) {
            $this->addDeserializationFailedFlashMessage();

            return $this->jsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        if (!$this->verifyCaptcha($input->getCaptchaToken(), $request)) {
            $this->addFlash('error', 'Sorry, we were unable to process your vote.');

            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        try {
            $output = $this->voteService->vote(
                $input,
                $this->getUserInterface(),
                $this->getRequestDetails($request)
            );
        } catch (UnexpectedValueException $e) {
            return $this->jsonResponse(null, Response::HTTP_BAD_REQUEST);
        } catch (NotFoundException $e) {
            return $this->jsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        return $this->jsonResponse($output, Response::HTTP_CREATED);
    }
}
