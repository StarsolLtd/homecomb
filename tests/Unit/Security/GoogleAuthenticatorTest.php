<?php

namespace App\Tests\Unit\Security;

use App\Entity\Agency;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\GoogleAuthenticator;
use App\Service\UserRegistrationService;
use App\Tests\Unit\EntityManagerTrait;
use Doctrine\ORM\EntityManager;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Token\AccessToken;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @covers \App\Security\GoogleAuthenticator
 */
final class GoogleAuthenticatorTest extends TestCase
{
    use EntityManagerTrait;
    use ProphecyTrait;

    private GoogleAuthenticator $googleAuthenticator;

    private ObjectProphecy $clientRegistry;
    private ObjectProphecy $userRepository;
    private ObjectProphecy $userRegistrationService;

    public function setUp(): void
    {
        $this->clientRegistry = $this->prophesize(ClientRegistry::class);
        $this->entityManager = $this->prophesize(EntityManager::class);
        $this->userRepository = $this->prophesize(UserRepository::class);
        $this->userRegistrationService = $this->prophesize(UserRegistrationService::class);

        $this->googleAuthenticator = new GoogleAuthenticator(
            $this->clientRegistry->reveal(),
            $this->entityManager->reveal(),
            $this->userRepository->reveal(),
            $this->userRegistrationService->reveal()
        );
    }

    /**
     * @covers \App\Security\GoogleAuthenticator::getUser
     * Test ACCESS_GRANTED for agency admin user of review agency
     */
    public function testGetUser1(): void
    {
        $googleUser = $this->prophesize(GoogleUser::class);
        $oAuth2Client = $this->prophesize(OAuth2ClientInterface::class);
        $credentials = $this->prophesize(AccessToken::class);
        $user = $this->prophesize(User::class);
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $userInterface = $this->prophesize(UserInterface::class);

        $this->clientRegistry->getClient('google')
            ->shouldBeCalledOnce()
            ->willReturn($oAuth2Client);

        $oAuth2Client->fetchUserFromToken($credentials)
            ->shouldBeCalledOnce()
            ->willReturn($googleUser);

        $googleUser->getEmail()->shouldBeCalledOnce()->willReturn('jack@starsol.co.uk');
        $googleUser->getId()->shouldBeCalledOnce()->willReturn('test-google-id');

        $this->userRepository->findOneBy(['googleId' => 'test-google-id'])
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $this->userRepository->findOneBy(['email' => 'jack@starsol.co.uk'])
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $this->userRegistrationService->registerFromGoogleUser($googleUser)
            ->shouldBeCalledOnce()
            ->willReturn($user);

        $this->assertEntitiesArePersistedAndFlush([$user]);

        $user->getUsername()->shouldBeCalledOnce()->willReturn('jack@starsol.co.uk');

        $userProvider->loadUserByIdentifier('jack@starsol.co.uk')
            ->shouldBeCalledOnce()
            ->willReturn($userInterface);

        $this->googleAuthenticator->getUser($credentials->reveal(), $userProvider->reveal());
    }
}
