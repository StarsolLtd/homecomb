<?php

namespace App\Controller;

use App\Security\EmailVerifier;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

final class RegistrationController extends AbstractController
{
    public function __construct(
        private UserService $userService,
        private EmailVerifier $emailVerifier,
    ) {
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request): Response
    {
        return $this->render('index.html.twig');
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
}
