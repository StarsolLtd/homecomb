<?php

namespace App\Service\User;

use App\Entity\User;
use App\Exception\UserException;
use App\Factory\FlatModelFactory;
use App\Model\User\Flat;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private FlatModelFactory $flatModelFactory,
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

    public function getFlatModelFromUserInterface(?UserInterface $user): ?Flat
    {
        $user = $this->getUserEntityOrNullFromUserInterface($user);

        if (null === $user) {
            return null;
        }

        return $this->flatModelFactory->getUserFlatModel($user);
    }
}
