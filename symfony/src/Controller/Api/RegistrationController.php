<?php

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Entity\User;
use App\Exception\ConflictException;
use App\Model\User\RegisterInput;
use App\Security\EmailVerifier;
use App\Security\LoginFormAuthenticator;
use App\Service\GoogleReCaptchaService;
use App\Service\UserService;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Serializer\SerializerInterface;

class RegistrationController extends AppController
{
    use VerifyCaptchaTrait;

    private GoogleReCaptchaService $googleReCaptchaService;
    private UserService $userService;
    private EmailVerifier $emailVerifier;
    private GuardAuthenticatorHandler $guardHandler;
    private LoginFormAuthenticator $authenticator;

    public function __construct(
        GoogleReCaptchaService $googleReCaptchaService,
        UserService $userService,
        EmailVerifier $emailVerifier,
        GuardAuthenticatorHandler $guardHandler,
        LoginFormAuthenticator $authenticator,
        SerializerInterface $serializer
    ) {
        $this->googleReCaptchaService = $googleReCaptchaService;
        $this->userService = $userService;
        $this->emailVerifier = $emailVerifier;
        $this->guardHandler = $guardHandler;
        $this->authenticator = $authenticator;
        $this->serializer = $serializer;
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
            $user = $this->userService->register($input);
        } catch (ConflictException $e) {
            $this->addFlash('warning', 'There is already a user account for this email address.');

            return $this->jsonResponse(null, Response::HTTP_CONFLICT);
        }

        $this->sendVerificationEmail($user);

        $token = $this->authenticator->createAuthenticatedToken($user, 'main');
        $this->guardHandler->authenticateWithToken($token, $request, 'main');

        $this->addFlash(
            'success',
            'Your registration was successful and you will receive an email to confirm your email address.'
        );

        return $this->jsonResponse(null, Response::HTTP_CREATED);
    }

    private function sendVerificationEmail(User $user): void
    {
        // TODO service for emails
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('mailer@homecomb.co.uk', 'HomeComb'))
                ->to($user->getEmail())
                ->subject('Please confirm your email for HomeComb')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
    }
}
