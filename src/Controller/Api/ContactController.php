<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Model\Contact\SubmitInput;
use App\Service\ContactService;
use App\Service\GoogleReCaptchaService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ContactController extends AppController
{
    use VerifyCaptchaTrait;

    private ContactService $contactService;

    public function __construct(
        GoogleReCaptchaService $googleReCaptchaService,
        ContactService $contactService,
        SerializerInterface $serializer
    ) {
        $this->googleReCaptchaService = $googleReCaptchaService;
        $this->contactService = $contactService;
        $this->serializer = $serializer;
    }

    /**
     * @Route (
     *     "/api/contact",
     *     name="api-contact",
     *     methods={"POST"}
     * )
     */
    public function submitContact(Request $request): JsonResponse
    {
        try {
            /** @var SubmitInput $input */
            $input = $this->serializer->deserialize($request->getContent(), SubmitInput::class, 'json');
        } catch (Exception $e) {
            $this->addDeserializationFailedFlashMessage();

            return $this->jsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        if (!$this->verifyCaptcha($input->getCaptchaToken(), $request)) {
            $this->addFlash('error', 'Sorry, we were unable to process your contact form submission.');

            return $this->jsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        $output = $this->contactService->submitContact($input);

        $this->addFlash(
            'success',
            'Thank you, your message has been sent to us successfully.'
        );

        return $this->jsonResponse($output, Response::HTTP_OK);
    }
}
