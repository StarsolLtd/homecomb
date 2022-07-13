<?php

namespace App\Tests\Unit\Service\User;

use App\Entity\User;
use App\Exception\UserException;
use App\Factory\FlatModelFactory;
use App\Model\User\Flat;
use App\Repository\UserRepository;
use App\Service\User\UserService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserServiceTest extends TestCase
{
    use ProphecyTrait;

    private UserService $userService;

    private ObjectProphecy $userRepository;
    private ObjectProphecy $flatModelFactory;

    public function setUp(): void
    {
        $this->userRepository = $this->prophesize(UserRepository::class);
        $this->flatModelFactory = $this->prophesize(FlatModelFactory::class);

        $this->userService = new UserService(
            $this->userRepository->reveal(),
            $this->flatModelFactory->reveal(),
        );
    }

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
     * Test returns null when user is null
     */
    public function testGetFlatModelFromUserInterface2(): void
    {
        $output = $this->userService->getFlatModelFromUserInterface(null);

        $this->assertNull($output);
    }

    /**
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
     * Test returns null when $user is null
     */
    public function testGetEntityFromInterface1(): void
    {
        $this->expectException(UserException::class);
        $output = $this->userService->getEntityFromInterface(null);

        $this->assertNull($output);
    }
}
