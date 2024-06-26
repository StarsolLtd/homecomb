<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class GoogleController extends AbstractController
{
    /**
     * @Route("/connect/google", schemes={"https"}, name="connect_google_start")
     */
    public function connectAction(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect(
                ['profile', 'email'],
                [],
            );
    }

    /**
     * @Route("/connect/google/check", schemes={"https"}, name="connect_google_check")
     */
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry): RedirectResponse
    {
        return $this->redirectToRoute('app_home');
    }
}
