<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class GoogleAuthenticator extends SocialAuthenticator
{
    public function __construct(
        private ClientRegistry $clientRegistry,
        private EntityManagerInterface $em,
        private UserRepository $userRepository,
        private UserService $userService,
    ) {
    }

    public function supports(Request $request): bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return 'connect_google_check' === $request->attributes->get('_route');
    }

    /**
     * @return \League\OAuth2\Client\Token\AccessToken|mixed
     */
    public function getCredentials(Request $request)
    {
        // this method is only called if supports() returns true

        return $this->fetchAccessToken($this->getGoogleClient());
    }

    /**
     * @return User|object|\Symfony\Component\Security\Core\User\UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var GoogleUser $googleUser */
        $googleUser = $this->getGoogleClient()->fetchUserFromToken($credentials);

        $email = $googleUser->getEmail();
        $googleId = $googleUser->getId();

        $user = $this->userRepository->findOneBy(['googleId' => $googleId]);

        if (!$user) {
            $user = $this->userRepository->findOneBy(['email' => $email]);
            if ($user) {
                $user->setGoogleId($googleId);
            } else {
                $user = $this->userService->registerFromGoogleUser($googleUser);
                $this->em->persist($user);
            }
            $this->em->flush();
        }

        return $userProvider->loadUserByIdentifier($user->getUsername());
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): ?Response
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     */
    public function start(Request $request, ?AuthenticationException $authException = null): RedirectResponse
    {
        return new RedirectResponse(
            '/connect/', // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    private function getGoogleClient(): OAuth2ClientInterface
    {
        return $this->clientRegistry->getClient('google');
    }
}
