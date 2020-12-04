<?php

namespace App\Tests\Unit\Util;

use App\Entity\Flag;
use App\Exception\UnexpectedValueException;
use App\Model\Flag\SubmitInput;
use App\Service\FlagService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class FlagServiceTest extends TestCase
{
    use ProphecyTrait;

    private FlagService $flagService;

    private $entityManagerMock;

    public function setUp(): void
    {
        $this->entityManagerMock = $this->prophesize(EntityManagerInterface::class);

        $this->flagService = new FlagService(
            $this->entityManagerMock->reveal(),
        );
    }

    public function testSubmitFlagIsSuccessWithValidData(): void
    {
        $input = new SubmitInput('Review', 1, 'This is spam');

        $this->entityManagerMock->persist(Argument::type(Flag::class))->shouldBeCalledOnce();
        $this->entityManagerMock->flush()->shouldBeCalledOnce();

        $output = $this->flagService->submitFlag($input);

        $this->assertTrue($output->isSuccess());
    }

    public function testSubmitFlagThrowsExceptionWithInvalidEntityName(): void
    {
        $input = new SubmitInput('Chopsticks', 1, 'These are utensils for eating food');

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Chopsticks is not a valid flag entity name.');

        $output = $this->flagService->submitFlag($input);
    }
}
