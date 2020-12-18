<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use App\Security\LoginFormAuthenticator;
use App\Service\GoogleReCaptchaService;
use App\Service\UserService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private GoogleReCaptchaService $googleReCaptchaService;
    private UserService $userService;
    private EmailVerifier $emailVerifier;

    public function __construct(
        GoogleReCaptchaService $googleReCaptchaService,
        UserService $userService,
        EmailVerifier $emailVerifier
    ) {
        $this->googleReCaptchaService = $googleReCaptchaService;
        $this->userService = $userService;
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/api/register", name="api_register")
     */
    public function registerWithApi(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        GuardAuthenticatorHandler $guardHandler,
        LoginFormAuthenticator $authenticator
    ): JsonResponse {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if (!$this->verifyReCaptcha($form->get('googleReCaptchaToken')->getData(), $request)) {
            $this->addFlash('error', 'Sorry, we were unable to process your registration request.');

            return new JsonResponse(
                [
                    'success' => false,
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->addFlash('error', 'Sorry, there was an issue processing your registration request.');

            return new JsonResponse(
                [
                    'success' => false,
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $form->get('plainPassword')->getData()
            )
        );

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('mailer@homecomb.co.uk', 'HomeComb'))
                ->to($user->getEmail())
                ->subject('Please confirm your email for HomeComb')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );

        $token = $authenticator->createAuthenticatedToken($user, 'main');
        $guardHandler->authenticateWithToken($token, $request, 'main');

        $this->addFlash(
            'notice',
            'Your registration was successful and you will receive an email to confirm your email address.'
        );

        return new JsonResponse(
            [
                'success' => true,
            ],
            Response::HTTP_CREATED
        );
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            /** @var UserInterface|null $userInterface */
            $userInterface = $this->getUser();
            $this->emailVerifier->handleEmailConfirmation(
                $request,
                $this->userService->getEntityFromInterface($userInterface)
            );
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_home');
    }

    private function verifyReCaptcha(?string $token, Request $request): bool
    {
        return $this->googleReCaptchaService->verify($token, $request->getClientIp(), $request->getHost());
    }
}
