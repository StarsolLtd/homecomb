<?php

namespace App\Tests\Unit\Service;

use App\Entity\User;
use App\Factory\FlatModelFactory;
use App\Model\User\Flat;
use App\Repository\BranchRepository;
use App\Repository\UserRepository;
use App\Service\UserService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class UserServiceTest extends TestCase
{
    use ProphecyTrait;

    private UserService $userService;

    private $branchRepository;
    private $userRepository;
    private $flatModelFactory;

    public function setUp(): void
    {
        $this->branchRepository = $this->prophesize(BranchRepository::class);
        $this->userRepository = $this->prophesize(UserRepository::class);
        $this->flatModelFactory = $this->prophesize(FlatModelFactory::class);

        $this->userService = new UserService(
            $this->branchRepository->reveal(),
            $this->userRepository->reveal(),
            $this->flatModelFactory->reveal(),
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
}
