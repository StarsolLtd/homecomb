<?php

namespace App\Service;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Exception\ConflictException;
use App\Exception\ForbiddenException;
use App\Factory\BranchFactory;
use App\Model\Branch\CreateBranchInput;
use App\Model\Branch\CreateBranchOutput;
use App\Model\Branch\UpdateBranchInput;
use App\Model\Branch\UpdateBranchOutput;
use App\Repository\BranchRepository;
use App\Util\BranchHelper;
use Doctrine\ORM\EntityManagerInterface;
use function sprintf;
use Symfony\Component\Security\Core\User\UserInterface;

class BranchService
{
    public function __construct(
        private NotificationService $notificationService,
        private UserService $userService,
        private EntityManagerInterface $entityManager,
        private BranchFactory $branchFactory,
        private BranchHelper $branchHelper,
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

    public function updateBranch(string $slug, UpdateBranchInput $updateBranchInput, ?UserInterface $user): UpdateBranchOutput
    {
        $user = $this->userService->getEntityFromInterface($user);

        $branch = $this->branchRepository->findOneBySlugUserCanManage($slug, $user);

        $branch->setTelephone($updateBranchInput->getTelephone())
            ->setEmail($updateBranchInput->getEmail());

        $this->entityManager->flush();

        return new UpdateBranchOutput(true);
    }

    public function findOrCreate(string $branchName, ?Agency $agency): Branch
    {
        $branch = null === $agency
            ? $this->branchRepository->findOneByNameWithoutAgencyOrNull($branchName)
            : $this->branchRepository->findOneByNameAndAgencyOrNull($branchName, $agency);

        if (null !== $branch) {
            return $branch;
        }

        $branch = $this->create($branchName, $agency);

        $this->entityManager->persist($branch);
        $this->entityManager->flush();

        return $branch;
    }

    private function create(string $branchName, ?Agency $agency): Branch
    {
        $branch = (new Branch())
            ->setAgency($agency)
            ->setName($branchName);

        $this->branchHelper->generateSlug($branch);

        return $branch;
    }
}
