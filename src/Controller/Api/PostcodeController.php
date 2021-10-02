<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Model\Property\PostcodeInput;
use App\Service\GetAddressService;
use App\Service\GoogleReCaptchaService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class PostcodeController extends AppController
{
    use VerifyCaptchaTrait;

    public function __construct(
        private GetAddressService $getAddressService,
        private GoogleReCaptchaService $googleReCaptchaService,
        protected SerializerInterface $serializer,
    ) {
    }

    /**
     * @Route (
     *     "/api/postcode",
     *     name="api-postcode",
     *     methods={"POST"}
     * )
     */
    public function findAddressesInPostcode(Request $request): JsonResponse
    {
        try {
            /** @var PostcodeInput $input */
            $input = $this->serializer->deserialize($request->getContent(), PostcodeInput::class, 'json');
        } catch (Exception $e) {
            $this->addDeserializationFailedFlashMessage();

            return $this->jsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        if (!$this->verifyCaptcha($input->getCaptchaToken(), $request)) {
            $this->addFlash('error', 'Sorry, we were unable to process your request.');

            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $postcodeProperties = $this->getAddressService->find($input->getPostcode());

        return $this->jsonResponse($postcodeProperties, Response::HTTP_OK);
    }
}
