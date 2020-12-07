<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function getUserEntityFromUserInterface(?UserInterface $user): ?User
    {
        if (null === $user) {
            return null;
        }
        if ($user instanceof User) {
            return $user;
        }

        return $this->userRepository->loadUserByUsername($user->getUsername()) ?? null;
    }
}
