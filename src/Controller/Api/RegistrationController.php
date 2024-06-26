<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Exception\ConflictException;
use App\Model\User\RegisterInput;
use App\Security\EmailVerifier;
use App\Security\LoginFormAuthenticator;
use App\Service\GoogleReCaptchaService;
use App\Service\User\RegistrationService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Serializer\SerializerInterface;

final class RegistrationController extends AppController
{
    use VerifyCaptchaTrait;

    public const MESSAGE_REGISTRATION_SUCCESSFUL = 'Your registration was successful and you will receive an email to confirm your email address.';

    public function __construct(
        private GoogleReCaptchaService $googleReCaptchaService,
        private RegistrationService $registrationService,
        private EmailVerifier $emailVerifier,
        private GuardAuthenticatorHandler $guardHandler,
        private LoginFormAuthenticator $authenticator,
        protected SerializerInterface $serializer,
    ) {
    }

    /**
     * @Route (
     *     "/api/register",
     *     name="api-register",
     *     methods={"POST"}
     * )
     */
    public function register(Request $request): JsonResponse
    {
        try {
            /** @var RegisterInput $input */
            $input = $this->serializer->deserialize($request->getContent(), RegisterInput::class, 'json');
        } catch (Exception $e) {
            $this->addDeserializationFailedFlashMessage();

            return $this->jsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        if (!$this->verifyCaptcha($input->getCaptchaToken(), $request)) {
            $this->addFlash('error', 'Sorry, we were unable to process your registration request.');

            return $this->jsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = $this->registrationService->register($input);
        } catch (ConflictException $e) {
            $this->addFlash('warning', 'There is already a user account for this email address.');

            return $this->jsonResponse(null, Response::HTTP_CONFLICT);
        }

        $token = $this->authenticator->createAuthenticatedToken($user, 'main');
        $this->guardHandler->authenticateWithToken($token, $request, 'main');

        $this->addFlash('success', self::MESSAGE_REGISTRATION_SUCCESSFUL);

        return $this->jsonResponse(null, Response::HTTP_CREATED);
    }
}
