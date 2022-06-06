<?php

namespace App\Tests\Unit\Command;

use App\Command\SendReviewSolicitationEmailCommand;
use App\Entity\TenancyReviewSolicitation;
use App\Exception\NotFoundException;
use App\Repository\TenancyReviewSolicitationRepositoryInterface;
use App\Service\TenancyReviewSolicitation\SendService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use RuntimeException;
use Symfony\Component\Console\Command\Command;

final class SendReviewSolicitationEmailCommandTest extends TestCase
{
    use CommandTestTrait;
    use ProphecyTrait;

    private int $id = 234;

    private SendReviewSolicitationEmailCommand $command;

    private ObjectProphecy $tenancyReviewSolicitationRepository;
    private ObjectProphecy $sendService;

    public function setUp(): void
    {
        $this->tenancyReviewSolicitationRepository = $this->prophesize(TenancyReviewSolicitationRepositoryInterface::class);
        $this->sendService = $this->prophesize(SendService::class);

        $this->command = new SendReviewSolicitationEmailCommand(
            $this->tenancyReviewSolicitationRepository->reveal(),
            $this->sendService->reveal(),
        );

        $this->setupCommandTester('email:review-solicitation');
    }

    /**
     * Test happy path where tenancy review solicitation exists and email is sent.
     */
    public function testExecute1(): void
    {
        $tenancyReviewSolicitation = $this->prophesize(TenancyReviewSolicitation::class);
        $this->tenancyReviewSolicitationRepository->find($this->id)->shouldBeCalledOnce()->willReturn($tenancyReviewSolicitation);

        $this->sendService->send($tenancyReviewSolicitation)->shouldBeCalledOnce();

        $result = $this->commandTester->execute(['arg1' => (string) $this->id]);

        $this->assertEquals(Command::SUCCESS, $result);

        $display = $this->commandTester->getDisplay();

        $this->assertStringContainsString('Sending email for review solicitation '.$this->id, $display);
    }

    /**
     * Test an exception is thrown when tenancy review solicitation not found.
     */
    public function testExecute2(): void
    {
        $this->tenancyReviewSolicitationRepository->find($this->id)->shouldBeCalledOnce()->willReturn(null);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('TenancyReviewSolicitation '.$this->id.' not found.');

        $this->commandTester->execute(['arg1' => $this->id]);
    }

    /**
     * Test an exception is thrown when argument type is invalid.
     */
    public function testExecute3(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid type of arg1: boolean');

        $this->commandTester->execute(['arg1' => true]);
    }
}
