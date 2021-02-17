<?php

namespace App\Tests\Unit\Service;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\User;
use App\Exception\ConflictException;
use App\Exception\UserException;
use App\Factory\FlatModelFactory;
use App\Factory\UserFactory;
use App\Model\User\Flat;
use App\Model\User\RegisterInput;
use App\Repository\BranchRepository;
use App\Repository\UserRepository;
use App\Service\EmailService;
use App\Service\UserService;
use App\Tests\Unit\EntityManagerTrait;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\VerifyEmail\Model\VerifyEmailSignatureComponents;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

/**
 * @covers \App\Service\UserService
 */
class UserServiceTest extends TestCase
{
    use ProphecyTrait;
    use EntityManagerTrait;

    private UserService $userService;

    private $userFactory;
    private $branchRepository;
    private $userRepository;
    private $flatModelFactory;
    private $entityManager;
    private $verifyEmailHelper;
    private $emailService;

    public function setUp(): void
    {
        $this->userFactory = $this->prophesize(UserFactory::class);
        $this->branchRepository = $this->prophesize(BranchRepository::class);
        $this->userRepository = $this->prophesize(UserRepository::class);
        $this->flatModelFactory = $this->prophesize(FlatModelFactory::class);
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->verifyEmailHelper = $this->prophesize(VerifyEmailHelperInterface::class);
        $this->emailService = $this->prophesize(EmailService::class);

        $this->userService = new UserService(
            $this->userFactory->reveal(),
            $this->branchRepository->reveal(),
            $this->userRepository->reveal(),
            $this->flatModelFactory->reveal(),
            $this->entityManager->reveal(),
            $this->verifyEmailHelper->reveal(),
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
        $this->assertEntityManagerUnused();
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
        $this->assertEntityManagerUnused();
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
        $this->assertEntityManagerUnused();
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
        $this->assertEntityManagerUnused();
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
     * @covers \App\Service\UserService::register
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

        $this->userService->register($input->reveal());
    }

    /**
     * @covers \App\Service\UserService::register
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

        $this->userService->register($input->reveal());

        $this->assertEntityManagerUnused();
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

    /**
     * @covers \App\Service\UserService::sendVerificationEmail
     */
    public function testSendVerificationEmail1(): void
    {
        $user = $this->prophesizeSendVerificationEmail();

        $this->userService->sendVerificationEmail($user->reveal());
    }

    private function prophesizeSendVerificationEmail(): ObjectProphecy
    {
        $user = $this->prophesize(User::class);

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
