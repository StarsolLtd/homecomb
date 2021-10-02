<?php

namespace App\Service\User;

use App\Entity\User;
use App\Exception\UserException;
use App\Service\EmailService;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class ResetPasswordService
{
    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EmailService $emailService
    ) {
    }

    public function sendResetPasswordEmail(User $user): bool
    {
        $userEmail = $user->getEmail();
        $userFullName = $user->getFirstName().' '.$user->getLastName();

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            throw new UserException($e->getMessage());
        }

        $this->emailService->process(
            $userEmail,
            $userFullName,
            'HomeComb - Reset your password',
            'reset-password',
            [
                'resetToken' => $resetToken,
                'tokenLifetime' => $this->resetPasswordHelper->getTokenLifetime(),
            ],
            null,
            null,
            $user
        );

        return true;
    }
}
