<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Exception\NotFoundException;
use App\Exception\UnexpectedValueException;
use App\Factory\InteractionFactory;
use App\Model\Flag\SubmitInput;
use App\Service\FlagService;
use App\Service\GoogleReCaptchaService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class FlagController extends AppController
{
    use VerifyCaptchaTrait;

    public function __construct(
        private GoogleReCaptchaService $googleReCaptchaService,
        private FlagService $flagService,
        protected InteractionFactory $interactionFactory,
        protected SerializerInterface $serializer,
    ) {
    }

    /**
     * @Route (
     *     "/api/flag",
     *     name="flag",
     *     methods={"POST"}
     * )
     */
    public function submitFlag(Request $request): JsonResponse
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

        try {
            $output = $this->flagService->submitFlag(
                $input,
                $this->getUserInterface(),
                $this->getRequestDetails($request)
            );
        } catch (UnexpectedValueException $e) {
            return $this->jsonResponse(null, Response::HTTP_BAD_REQUEST);
        } catch (NotFoundException $e) {
            return $this->jsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $this->addFlash(
            'success',
            'Your report was received successfully and will be checked by our moderation team shortly.'
        );

        return $this->jsonResponse($output, Response::HTTP_CREATED);
    }
}
