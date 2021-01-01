<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Model\Flag\SubmitInput;
use App\Service\FlagService;
use App\Service\GoogleReCaptchaService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class FlagController extends AppController
{
    use VerifyCaptchaTrait;

    private FlagService $flagService;

    public function __construct(
        GoogleReCaptchaService $googleReCaptchaService,
        FlagService $flagService,
        SerializerInterface $serializer
    ) {
        $this->googleReCaptchaService = $googleReCaptchaService;
        $this->flagService = $flagService;
        $this->serializer = $serializer;
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

        $output = $this->flagService->submitFlag($input, $this->getUserInterface());

        $this->addFlash(
            'success',
            'Your report was received successfully and will be checked by our moderation team shortly.'
        );

        return $this->jsonResponse($output, Response::HTTP_CREATED);
    }
}
