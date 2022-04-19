<?php

namespace App\Service\Branch;

use App\Repository\BranchRepositoryInterface;
use App\Service\User\UserService;
use Symfony\Component\Security\Core\User\UserInterface;

class BranchAdminService
{
    public function __construct(
        private BranchRepositoryInterface $branchRepository,
        private UserService $userService,
    ) {
    }

    public function isUserBranchAdmin(string $branchSlug, ?UserInterface $user): bool
    {
        $user = $this->userService->getEntityFromInterface($user);
        $agency = $user->getAdminAgency();
        if (null === $agency) {
            return false;
        }

        $branch = $this->branchRepository->findOnePublishedBySlug($branchSlug);
        $branchAgency = $branch->getAgency();
        if (null === $branchAgency) {
            return false;
        }
        if ($branchAgency->getId() !== $agency->getId()) {
            return false;
        }

        return true;
    }
}
