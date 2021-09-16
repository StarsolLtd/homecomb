<?php

namespace App\Service;

use App\Entity\User;
use App\Exception\UserException;
use App\Factory\FlatModelFactory;
use App\Model\User\Flat;
use App\Repository\BranchRepository;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class UserService
{
    public function __construct(
        private BranchRepository $branchRepository,
        private UserRepository $userRepository,
        private FlatModelFactory $flatModelFactory,
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EmailService $emailService
    ) {
    }

    public function getUserEntityOrNullFromUserInterface(?UserInterface $user): ?User
    {
        if (null === $user) {
            return null;
        }
        if ($user instanceof User) {
            return $user;
        }

        return $this->userRepository->loadUserByUsername($user->getUsername()) ?? null;
    }

    public function getEntityFromInterface(?UserInterface $user): User
    {
        $userEntity = $this->getUserEntityOrNullFromUserInterface($user);
        if (null === $userEntity) {
            throw new UserException('User entity not found.');
        }

        return $userEntity;
    }

    public function isUserBranchAdmin(string $branchSlug, ?UserInterface $user): bool
    {
        $user = $this->getEntityFromInterface($user);
        $agency = $user->getAdminAgency();
        if (null === $agency) {
            return false;
        }

        $branch = $this->branchRepository->findOnePublishedBySlug($branchSlug);
        $branchAgency = $branch->getAgency();
        if (null == $branchAgency) {
            return false;
        }
        if ($branchAgency->getId() !== $agency->getId()) {
            return false;
        }

        return true;
    }

    public function getFlatModelFromUserInterface(?UserInterface $user): ?Flat
    {
        $user = $this->getUserEntityOrNullFromUserInterface($user);

        if (null === $user) {
            return null;
        }

        return $this->flatModelFactory->getUserFlatModel($user);
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
