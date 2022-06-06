<?php

namespace App\Tests\Unit\Command;

use App\Command\SendVerificationEmailCommand;
use App\Entity\User;
use App\Exception\NotFoundException;
use App\Repository\UserRepository;
use App\Service\User\RegistrationService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use RuntimeException;
use Symfony\Component\Console\Command\Command;

final class SendVerificationEmailCommandTest extends TestCase
{
    use CommandTestTrait;
    use ProphecyTrait;

    private int $userId = 234;

    private SendVerificationEmailCommand $command;

    private ObjectProphecy $userRepository;
    private ObjectProphecy $registrationService;

    public function setUp(): void
    {
        $this->userRepository = $this->prophesize(UserRepository::class);
        $this->registrationService = $this->prophesize(RegistrationService::class);

        $this->command = new SendVerificationEmailCommand(
            $this->userRepository->reveal(),
            $this->registrationService->reveal(),
        );

        $this->setupCommandTester('email:verification');
    }

    /**
     * Test happy path where user exists and email is sent.
     */
    public function testExecute1(): void
    {
        $user = $this->prophesizeReturnUser();

        $this->registrationService->sendVerificationEmail($user)->shouldBeCalledOnce()->willReturn(true);

        $result = $this->commandTester->execute(['arg1' => (string) $this->userId]);

        $this->assertEquals(Command::SUCCESS, $result);

        $display = $this->commandTester->getDisplay();

        $this->assertStringContainsString('Sending verification email for user '.$this->userId, $display);
        $this->assertStringContainsString('Email sent.', $display);
    }

    /**
     * Test an exception is thrown when the supplied user ID is not found.
     */
    public function testExecute2(): void
    {
        $this->userRepository->find($this->userId)->shouldBeCalledOnce()->willReturn(null);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('User '.$this->userId.' not found.');

        $this->commandTester->execute(['arg1' => $this->userId]);
    }

    /**
     * Test failure code is returned when email does not send.
     */
    public function testExecute3(): void
    {
        $user = $this->prophesizeReturnUser();

        $this->registrationService->sendVerificationEmail($user)->shouldBeCalledOnce()->willReturn(false);

        $result = $this->commandTester->execute(['arg1' => $this->userId]);

        $this->assertEquals(Command::FAILURE, $result);

        $display = $this->commandTester->getDisplay();

        $this->assertStringContainsString('Sending verification email for user '.$this->userId, $display);
        $this->assertStringContainsString('Email not sent.', $display);
    }

    /**
     * Test an exception is thrown when argument type is invalid.
     */
    public function testExecute4(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid type of arg1: boolean');

        $this->commandTester->execute(['arg1' => true]);
    }

    private function prophesizeReturnUser(): ObjectProphecy
    {
        $user = $this->prophesize(User::class);
        $this->userRepository->find($this->userId)->shouldBeCalledOnce()->willReturn($user);

        return $user;
    }
}
