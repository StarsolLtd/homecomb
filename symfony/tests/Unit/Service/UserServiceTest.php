<?php

namespace App\Tests\Unit\Service;

use App\Entity\User;
use App\Exception\ConflictException;
use App\Factory\FlatModelFactory;
use App\Factory\UserFactory;
use App\Model\User\Flat;
use App\Model\User\RegisterInput;
use App\Repository\BranchRepository;
use App\Repository\UserRepository;
use App\Service\UserService;
use App\Tests\Unit\EntityManagerTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

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

    public function setUp(): void
    {
        $this->userFactory = $this->prophesize(UserFactory::class);
        $this->branchRepository = $this->prophesize(BranchRepository::class);
        $this->userRepository = $this->prophesize(UserRepository::class);
        $this->flatModelFactory = $this->prophesize(FlatModelFactory::class);
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);

        $this->userService = new UserService(
            $this->userFactory->reveal(),
            $this->branchRepository->reveal(),
            $this->userRepository->reveal(),
            $this->flatModelFactory->reveal(),
            $this->entityManager->reveal(),
        );
    }

    public function testGetFlatModelFromUserInterface(): void
    {
        $user = (new User())->setEmail('jack@starsol.co.uk');
        $userModel = $this->prophesize(Flat::class);

        $this->flatModelFactory->getUserFlatModel($user)
            ->shouldBeCalledOnce()
            ->willReturn($userModel);

        $this->userService->getFlatModelFromUserInterface($user);
    }

    public function testRegister(): void
    {
        $input = $this->prophesize(RegisterInput::class);
        $user = $this->prophesize(User::class);

        $input->getEmail()
            ->shouldBeCalled()
            ->willReturn('test@starsol.co.uk');

        $this->userRepository->loadUserByUsername('test@starsol.co.uk')
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

    public function testRegisterThrowsConflictExceptionIfAlreadyExists(): void
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
}
