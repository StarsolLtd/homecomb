<?php

namespace App\Service\Branch;

use App\Exception\ConflictException;
use App\Exception\ForbiddenException;
use App\Factory\BranchFactory;
use App\Model\Branch\CreateBranchOutput;
use App\Model\Branch\CreateInputInterface;
use App\Repository\BranchRepositoryInterface;
use App\Service\NotificationService;
use App\Service\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CreateService
{
    public function __construct(
        private NotificationService $notificationService,
        private UserService $userService,
        private EntityManagerInterface $entityManager,
        private BranchFactory $branchFactory,
        private BranchRepositoryInterface $branchRepository
    ) {
    }

    public function createBranch(CreateInputInterface $input, ?UserInterface $user): CreateBranchOutput
    {
        $user = $this->userService->getEntityFromInterface($user);

        $agency = $user->getAdminAgency();

        if (null === $agency) {
            throw new ForbiddenException(sprintf('Logged in user %s is not the admin of an agency.', $user->getUsername()));
        }

        $branchName = $input->getBranchName();
        $alreadyExists = $this->branchRepository->findOneByNameAndAgencyOrNull($branchName, $agency);
        if (null !== $alreadyExists) {
            throw new ConflictException('A branch with the name '.$branchName.' already exists for this agency.');
        }

        $branch = $this->branchFactory->createEntityFromCreateBranchInput($input, $agency);

        $this->entityManager->persist($branch);
        $this->entityManager->flush();

        $this->notificationService->sendBranchModerationNotification($branch);

        return new CreateBranchOutput(true);
    }
}
