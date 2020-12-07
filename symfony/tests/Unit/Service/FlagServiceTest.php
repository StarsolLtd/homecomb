<?php

namespace App\Tests\Unit\Util;

use App\Entity\Flag;
use App\Entity\User;
use App\Exception\UnexpectedValueException;
use App\Model\Flag\SubmitInput;
use App\Service\FlagService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class FlagServiceTest extends TestCase
{
    use ProphecyTrait;

    private FlagService $flagService;

    private $entityManagerMock;
    private $userServiceMock;

    public function setUp(): void
    {
        $this->entityManagerMock = $this->prophesize(EntityManagerInterface::class);
        $this->userServiceMock = $this->prophesize(UserService::class);

        $this->flagService = new FlagService(
            $this->entityManagerMock->reveal(),
            $this->userServiceMock->reveal(),
        );
    }

    public function testSubmitFlagIsSuccessWithValidData(): void
    {
        $input = new SubmitInput('Review', 1, 'This is spam');

        $this->entityManagerMock->persist(Argument::type(Flag::class))->shouldBeCalledOnce();
        $this->entityManagerMock->flush()->shouldBeCalledOnce();

        $output = $this->flagService->submitFlag($input, null);

        $this->assertTrue($output->isSuccess());
    }

    public function testSubmitFlagThrowsExceptionWithInvalidEntityName(): void
    {
        $input = new SubmitInput('Chopsticks', 1, 'These are utensils for eating food');

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Chopsticks is not a valid flag entity name.');

        $output = $this->flagService->submitFlag($input, null);
    }

    public function testSubmitFlagIsSuccessWithUserAndValidData(): void
    {
        $input = new SubmitInput('Review', 1, 'This is spam');
        $user = (new User())->setEmail('jack@starsol.co.uk');

        $this->entityManagerMock->persist(Argument::type(Flag::class))->shouldBeCalledOnce();
        $this->entityManagerMock->flush()->shouldBeCalledOnce();

        $this->userServiceMock->getUserEntityFromUserInterface($user)->shouldBeCalledOnce()->willReturn($user);

        $output = $this->flagService->submitFlag($input, $user);

        $this->assertTrue($output->isSuccess());
    }
}
