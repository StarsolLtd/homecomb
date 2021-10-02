<?php

namespace App\Service\Branch;

use App\Model\Branch\UpdateBranchInput;
use App\Model\Branch\UpdateBranchOutput;
use App\Repository\BranchRepository;
use App\Service\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class BranchUpdateService
{
    public function __construct(
        private UserService $userService,
        private EntityManagerInterface $entityManager,
        private BranchRepository $branchRepository
    ) {
    }

    public function updateBranch(string $slug, UpdateBranchInput $updateBranchInput, ?UserInterface $user): UpdateBranchOutput
    {
        $user = $this->userService->getEntityFromInterface($user);

        $branch = $this->branchRepository->findOneBySlugUserCanManage($slug, $user);

        $branch->setTelephone($updateBranchInput->getTelephone())
            ->setEmail($updateBranchInput->getEmail());

        $this->entityManager->flush();

        return new UpdateBranchOutput(true);
    }
}
