<?php

namespace App\Service;

use App\Entity\User;
use App\Factory\FlatModelFactory;
use App\Model\User\Flat;
use App\Repository\BranchRepository;
use App\Repository\UserRepository;
use RuntimeException;
use Symfony\Component\Security\Core\User\UserInterface;

class UserService
{
    private BranchRepository $branchRepository;
    private UserRepository $userRepository;
    private FlatModelFactory $flatModelFactory;

    public function __construct(
        BranchRepository $branchRepository,
        UserRepository $userRepository,
        FlatModelFactory $flatModelFactory
    ) {
        $this->branchRepository = $branchRepository;
        $this->userRepository = $userRepository;
        $this->flatModelFactory = $flatModelFactory;
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
            throw new RuntimeException('User entity not found.');
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
}
