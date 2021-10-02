<?php

namespace App\Service;

use App\Exception\ConflictException;
use App\Exception\ForbiddenException;
use App\Factory\BranchFactory;
use App\Model\Branch\CreateBranchInput;
use App\Model\Branch\CreateBranchOutput;
use App\Repository\BranchRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class BranchService
{
    public function __construct(
        private NotificationService $notificationService,
        private UserService $userService,
        private EntityManagerInterface $entityManager,
        private BranchFactory $branchFactory,
        private BranchRepository $branchRepository
    ) {
    }

    public function createBranch(CreateBranchInput $createBranchInput, ?UserInterface $user): CreateBranchOutput
    {
        $user = $this->userService->getEntityFromInterface($user);

        $agency = $user->getAdminAgency();

        if (null === $agency) {
            throw new ForbiddenException(sprintf('Logged in user %s is not the admin of an agency.', $user->getUsername()));
        }

        $branchName = $createBranchInput->getBranchName();
        $alreadyExists = $this->branchRepository->findOneByNameAndAgencyOrNull($branchName, $agency);
        if (null !== $alreadyExists) {
            throw new ConflictException('A branch with the name '.$branchName.' already exists for this agency.');
        }

        $branch = $this->branchFactory->createEntityFromCreateBranchInput($createBranchInput, $agency);

        $this->entityManager->persist($branch);
        $this->entityManager->flush();

        $this->notificationService->sendBranchModerationNotification($branch);

        return new CreateBranchOutput(true);
    }
}
