<?php

namespace App\Tests\Unit\Service;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\User;
use App\Exception\UserException;
use App\Factory\FlatModelFactory;
use App\Model\User\Flat;
use App\Repository\BranchRepository;
use App\Repository\UserRepository;
use App\Service\EmailService;
use App\Service\UserService;
use DateTime;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use SymfonyCasts\Bundle\VerifyEmail\Model\VerifyEmailSignatureComponents;

/**
 * @covers \App\Service\UserService
 */
final class UserServiceTest extends TestCase
{
    use ProphecyTrait;

    private UserService $userService;

    private ObjectProphecy $branchRepository;
    private ObjectProphecy $userRepository;
    private ObjectProphecy $flatModelFactory;
    private ObjectProphecy $resetPasswordHelper;
    private ObjectProphecy $emailService;

    public function setUp(): void
    {
        $this->branchRepository = $this->prophesize(BranchRepository::class);
        $this->userRepository = $this->prophesize(UserRepository::class);
        $this->flatModelFactory = $this->prophesize(FlatModelFactory::class);
        $this->resetPasswordHelper = $this->prophesize(ResetPasswordHelperInterface::class);
        $this->emailService = $this->prophesize(EmailService::class);

        $this->userService = new UserService(
            $this->branchRepository->reveal(),
            $this->userRepository->reveal(),
            $this->flatModelFactory->reveal(),
            $this->resetPasswordHelper->reveal(),
            $this->emailService->reveal(),
        );
    }

    /**
     * @covers \App\Service\UserService::isUserBranchAdmin
     * Test returns true when the user's admin agency ID matches the branch's agency ID
     */
    public function testIsUserBranchAdmin1(): void
    {
        $user = $this->prophesize(User::class);
        $branch = $this->prophesize(Branch::class);
        $agency = $this->prophesize(Agency::class);
        $branchAgency = $this->prophesize(Agency::class);

        $user->getAdminAgency()->shouldBeCalled()->willReturn($agency);
        $branch->getAgency()->shouldBeCalled()->willReturn($branchAgency);
        $branchAgency->getId()->shouldBeCalled()->willReturn(42);
        $agency->getId()->shouldBeCalled()->willReturn(42);

        $this->branchRepository->findOnePublishedBySlug('branchslug')
            ->shouldBeCalled()
            ->willReturn($branch);

        $output = $this->userService->isUserBranchAdmin('branchslug', $user->reveal());

        $this->assertTrue($output);
    }

    /**
     * @covers \App\Service\UserService::isUserBranchAdmin
     * Test returns false when the user's admin agency ID does not match the branch's agency ID
     */
    public function testIsUserBranchAdmin2(): void
    {
        $user = $this->prophesize(User::class);
        $branch = $this->prophesize(Branch::class);
        $agency = $this->prophesize(Agency::class);
        $branchAgency = $this->prophesize(Agency::class);

        $user->getAdminAgency()->shouldBeCalled()->willReturn($agency);
        $branch->getAgency()->shouldBeCalled()->willReturn($branchAgency);
        $branchAgency->getId()->shouldBeCalled()->willReturn(42);
        $agency->getId()->shouldBeCalled()->willReturn(88);

        $this->branchRepository->findOnePublishedBySlug('branchslug')
            ->shouldBeCalled()
            ->willReturn($branch);

        $output = $this->userService->isUserBranchAdmin('branchslug', $user->reveal());

        $this->assertFalse($output);
    }

    /**
     * @covers \App\Service\UserService::isUserBranchAdmin
     * Test returns false when the user is not an agency admin
     */
    public function testIsUserBranchAdmin3(): void
    {
        $user = $this->prophesize(User::class);

        $user->getAdminAgency()->shouldBeCalled()->willReturn(null);

        $output = $this->userService->isUserBranchAdmin('branchslug', $user->reveal());

        $this->assertFalse($output);
    }

    /**
     * @covers \App\Service\UserService::isUserBranchAdmin
     * Test returns false when the branch is not associated with an agency
     */
    public function testIsUserBranchAdmin4(): void
    {
        $user = $this->prophesize(User::class);
        $branch = $this->prophesize(Branch::class);
        $agency = $this->prophesize(Agency::class);

        $user->getAdminAgency()->shouldBeCalled()->willReturn($agency);
        $branch->getAgency()->shouldBeCalled()->willReturn(null);

        $this->branchRepository->findOnePublishedBySlug('branchslug')
            ->shouldBeCalled()
            ->willReturn($branch);

        $output = $this->userService->isUserBranchAdmin('branchslug', $user->reveal());

        $this->assertFalse($output);
    }

    /**
     * @covers \App\Service\UserService::getFlatModelFromUserInterface
     */
    public function testGetFlatModelFromUserInterface1(): void
    {
        $user = (new User())->setEmail('jack@starsol.co.uk');
        $userModel = $this->prophesize(Flat::class);

        $this->flatModelFactory->getUserFlatModel($user)
            ->shouldBeCalledOnce()
            ->willReturn($userModel);

        $this->userService->getFlatModelFromUserInterface($user);
    }

    /**
     * @covers \App\Service\UserService::getFlatModelFromUserInterface
     * Test returns null when user is null
     */
    public function testGetFlatModelFromUserInterface2(): void
    {
        $output = $this->userService->getFlatModelFromUserInterface(null);

        $this->assertNull($output);
    }

    /**
     * @covers \App\Service\UserService::getUserEntityOrNullFromUserInterface
     * Test gets user entity from repository when $user is not already an entity but does implement UserInterface
     */
    public function testGetUserEntityOrNullFromUserInterface1(): void
    {
        $userInterface = $this->prophesize(UserInterface::class);
        $userEntity = new User();

        $userInterface->getUsername()->shouldBeCalled()->willReturn('test.user@starsol.co.uk');

        $this->userRepository->loadUserByUsername('test.user@starsol.co.uk')
            ->shouldBeCalledOnce()
            ->willReturn($userEntity);

        $output = $this->userService->getUserEntityOrNullFromUserInterface($userInterface->reveal());

        $this->assertEquals($userEntity, $output);
    }

    /**
     * @covers \App\Service\UserService::getEntityFromInterface
     * Test returns null when $user is null
     */
    public function testGetEntityFromInterface1(): void
    {
        $this->expectException(UserException::class);
        $output = $this->userService->getEntityFromInterface(null);

        $this->assertNull($output);
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
