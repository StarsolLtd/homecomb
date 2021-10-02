<?php

namespace App\Tests\Unit\Service\User;

use App\Entity\User;
use App\Service\EmailService;
use App\Service\User\ResetPasswordService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

/**
 * @covers \App\Service\User\ResetPasswordService
 */
final class ResetPasswordServiceTest extends TestCase
{
    use ProphecyTrait;

    private ResetPasswordService $resetPasswordService;

    private ObjectProphecy $resetPasswordHelper;
    private ObjectProphecy $emailService;

    public function setUp(): void
    {
        $this->resetPasswordHelper = $this->prophesize(ResetPasswordHelperInterface::class);
        $this->emailService = $this->prophesize(EmailService::class);

        $this->resetPasswordService = new ResetPasswordService(
            $this->resetPasswordHelper->reveal(),
            $this->emailService->reveal(),
        );
    }

    /**
     * @covers \App\Service\User\ResetPasswordService::sendResetPasswordEmail
     */
    public function testSendResetPasswordEmail1(): void
    {
        $user = $this->prophesize(User::class);

        $user->getEmail()->shouldBeCalledOnce()->willReturn('turanga.leela@planet-express.com');
        $user->getFirstName()->shouldBeCalledOnce()->willReturn('Turanga');
        $user->getLastName()->shouldBeCalledOnce()->willReturn('Leela');

        $resetPasswordToken = $this->prophesize(ResetPasswordToken::class);

        $this->resetPasswordHelper->generateResetToken($user)
            ->shouldBeCalledOnce()
            ->willReturn($resetPasswordToken);

        $this->resetPasswordHelper->getTokenLifetime()
            ->shouldBeCalledOnce()
            ->willReturn(120);

        $this->emailService->process(
            'turanga.leela@planet-express.com',
            'Turanga Leela',
            'HomeComb - Reset your password',
            'reset-password',
            [
                'resetToken' => $resetPasswordToken,
                'tokenLifetime' => 120,
            ],
            null,
            null,
            $user
        )->shouldBeCalledOnce();

        $output = $this->resetPasswordService->sendResetPasswordEmail($user->reveal());

        $this->assertTrue($output);
    }
}
