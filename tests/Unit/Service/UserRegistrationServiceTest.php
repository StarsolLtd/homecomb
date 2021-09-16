<?php

namespace App\Tests\Unit\Service;

use App\Entity\User;
use App\Exception\ConflictException;
use App\Exception\UserException;
use App\Factory\UserFactory;
use App\Model\User\RegisterInput;
use App\Repository\UserRepository;
use App\Service\EmailService;
use App\Service\UserRegistrationService;
use App\Tests\Unit\EntityManagerTrait;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Client\Provider\GoogleUser;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use SymfonyCasts\Bundle\VerifyEmail\Model\VerifyEmailSignatureComponents;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

/**
 * @covers \App\Service\UserRegistrationService
 */
class UserRegistrationServiceTest extends TestCase
{
    use ProphecyTrait;
    use EntityManagerTrait;

    private UserRegistrationService $userRegistrationService;

    private ObjectProphecy $userFactory;
    private ObjectProphecy $userRepository;
    private ObjectProphecy $entityManager;
    private ObjectProphecy $resetPasswordHelper;
    private ObjectProphecy $verifyEmailHelper;
    private ObjectProphecy $emailService;

    public function setUp(): void
    {
        $this->userFactory = $this->prophesize(UserFactory::class);
        $this->userRepository = $this->prophesize(UserRepository::class);
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->resetPasswordHelper = $this->prophesize(ResetPasswordHelperInterface::class);
        $this->verifyEmailHelper = $this->prophesize(VerifyEmailHelperInterface::class);
        $this->emailService = $this->prophesize(EmailService::class);

        $this->userRegistrationService = new UserRegistrationService(
            $this->userFactory->reveal(),
            $this->userRepository->reveal(),
            $this->entityManager->reveal(),
            $this->resetPasswordHelper->reveal(),
            $this->verifyEmailHelper->reveal(),
            $this->emailService->reveal(),
        );
    }

    /**
     * @covers \App\Service\UserRegistrationService::register
     */
    public function testRegister1(): void
    {
        $input = $this->prophesize(RegisterInput::class);
        $user = $this->prophesizeSendVerificationEmail();

        $input->getEmail()
            ->shouldBeCalled()
            ->willReturn('turanga.leela@planet-express.com');

        $this->userRepository->loadUserByUsername('turanga.leela@planet-express.com')
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $this->userFactory->createEntityFromRegisterInput($input)
            ->shouldBeCalled()
            ->willReturn($user);

        $this->entityManager->persist($user)
            ->shouldBeCalledOnce();

        $this->entityManager->flush()
            ->shouldBeCalledOnce();

        $this->userRegistrationService->register($input->reveal());
    }

    /**
     * @covers \App\Service\UserRegistrationService::register
     * Test throws ConflictException if already exists
     */
    public function testRegister2(): void
    {
        $input = $this->prophesize(RegisterInput::class);
        $existingUser = $this->prophesize(User::class);

        $input->getEmail()
            ->shouldBeCalled()
            ->willReturn('test@starsol.co.uk');

        $this->userRepository->loadUserByUsername('test@starsol.co.uk')
            ->shouldBeCalledOnce()
            ->willReturn($existingUser);

        $this->expectException(ConflictException::class);

        $this->userRegistrationService->register($input->reveal());

        $this->assertEntityManagerUnused();
    }

    /**
     * @covers \App\Service\UserRegistrationService::registerFromGoogleUser
     */
    public function testRegisterFromGoogleUser1(): void
    {
        $googleUser = $this->prophesize(GoogleUser::class);

        $googleUser->getEmail()->shouldBeCalled()->willReturn('turanga.leela@planet-express.com');
        $googleUser->getId()->shouldBeCalled()->willReturn('test-google-id');
        $googleUser->getFirstName()->shouldBeCalled()->willReturn('Turanga');
        $googleUser->getLastName()->shouldBeCalled()->willReturn('Leela');

        $this->userRepository->loadUserByUsername('turanga.leela@planet-express.com')
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $this->entityManager->persist(Argument::type(User::class))->shouldBeCalledOnce();
        $this->entityManager->flush()->shouldBeCalledOnce();

        $user = $this->userRegistrationService->registerFromGoogleUser($googleUser->reveal());

        $this->assertEquals('turanga.leela@planet-express.com', $user->getEmail());
        $this->assertEquals('test-google-id', $user->getGoogleId());
        $this->assertEquals('Turanga', $user->getFirstName());
        $this->assertEquals('Leela', $user->getLastName());
        $this->assertTrue($user->isVerified());
    }

    /**
     * @covers \App\Service\UserRegistrationService::registerFromGoogleUser
     * Test throws ConflictException if already exists
     */
    public function testRegisterFromGoogleUser2(): void
    {
        $googleUser = $this->prophesize(GoogleUser::class);
        $existingUser = $this->prophesize(User::class);

        $googleUser->getEmail()->shouldBeCalled()->willReturn('test@starsol.co.uk');

        $this->userRepository->loadUserByUsername('test@starsol.co.uk')
            ->shouldBeCalledOnce()
            ->willReturn($existingUser);

        $this->expectException(ConflictException::class);

        $this->userRegistrationService->registerFromGoogleUser($googleUser->reveal());

        $this->assertEntityManagerUnused();
    }

    /**
     * @covers \App\Service\UserRegistrationService::registerFromGoogleUser
     * Test throws UserException if email is null
     */
    public function testRegisterFromGoogleUser3(): void
    {
        $googleUser = $this->prophesize(GoogleUser::class);

        $googleUser->getEmail()->shouldBeCalled()->willReturn(null);

        $this->expectException(UserException::class);

        $this->userRegistrationService->registerFromGoogleUser($googleUser->reveal());

        $this->assertEntityManagerUnused();
    }

    /**
     * @covers \App\Service\UserRegistrationService::sendVerificationEmail
     */
    public function testSendVerificationEmail1(): void
    {
        $user = $this->prophesizeSendVerificationEmail();

        $output = $this->userRegistrationService->sendVerificationEmail($user->reveal());

        $this->assertTrue($output);
    }

    private function prophesizeSendVerificationEmail(): ObjectProphecy
    {
        $user = $this->prophesize(User::class);

        $user->isVerified()->shouldBeCalledOnce()->willReturn(false);
        $user->getId()->shouldBeCalledOnce()->willReturn(5678);
        $user->getEmail()->shouldBeCalledOnce()->willReturn('turanga.leela@planet-express.com');
        $user->getFirstName()->shouldBeCalledOnce()->willReturn('Turanga');
        $user->getLastName()->shouldBeCalledOnce()->willReturn('Leela');

        $signatureComponents = $this->prophesize(VerifyEmailSignatureComponents::class);

        $expiresAt = $this->prophesize(DateTime::class);

        $this->verifyEmailHelper->generateSignature('app_verify_email', '5678', 'turanga.leela@planet-express.com')
            ->shouldBeCalledOnce()
            ->willReturn($signatureComponents);

        $signatureComponents->getSignedUrl()
            ->shouldBeCalledOnce()
            ->willReturn('http://test.homecomb.net/test');

        $signatureComponents->getExpiresAt()
            ->shouldBeCalledOnce()
            ->willReturn($expiresAt);

        $this->emailService->process(
            'turanga.leela@planet-express.com',
            'Turanga Leela',
            'Welcome to HomeComb! Please verify your email address',
            'email-verification',
            [
                'signedUrl' => 'http://test.homecomb.net/test',
                'expiresAt' => $expiresAt,
            ],
            null,
            $user,
            $user
        )->shouldBeCalledOnce();

        return $user;
    }
}
