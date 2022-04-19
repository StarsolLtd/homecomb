<?php

namespace App\Service\Branch;

use App\Model\Branch\UpdateBranchOutput;
use App\Model\Branch\UpdateInputInterface;
use App\Repository\BranchRepositoryInterface;
use App\Service\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UpdateService
{
    public function __construct(
        private UserService $userService,
        private EntityManagerInterface $entityManager,
        private BranchRepositoryInterface $branchRepository,
    ) {
    }

    public function updateBranch(string $slug, UpdateInputInterface $input, ?UserInterface $user): UpdateBranchOutput
    {
        $user = $this->userService->getEntityFromInterface($user);

        $branch = $this->branchRepository->findOneBySlugUserCanManage($slug, $user);

        $branch->setTelephone($input->getTelephone())
            ->setEmail($input->getEmail());

        $this->entityManager->flush();

        return new UpdateBranchOutput(true);
    }
}
