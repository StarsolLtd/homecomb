<?php

namespace App\Service;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Factory\BranchFactory;
use App\Model\Branch\CreateBranchInput;
use App\Model\Branch\CreateBranchOutput;
use App\Repository\AgencyRepository;
use App\Repository\BranchRepository;
use App\Util\BranchHelper;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;

class BranchService
{
    private NotificationService $notificationService;
    private UserService $userService;
    private EntityManagerInterface $entityManager;
    private AgencyRepository $agencyRepository;
    private BranchFactory $branchFactory;
    private BranchHelper $branchHelper;
    private BranchRepository $branchRepository;

    public function __construct(
        NotificationService $notificationService,
        UserService $userService,
        EntityManagerInterface $entityManager,
        AgencyRepository $agencyRepository,
        BranchFactory $branchFactory,
        BranchHelper $branchHelper,
        BranchRepository $branchRepository
    ) {
        $this->notificationService = $notificationService;
        $this->userService = $userService;
        $this->entityManager = $entityManager;
        $this->agencyRepository = $agencyRepository;
        $this->branchFactory = $branchFactory;
        $this->branchHelper = $branchHelper;
        $this->branchRepository = $branchRepository;
    }

    public function createBranch(CreateBranchInput $createBranchInput, ?UserInterface $user): CreateBranchOutput
    {
        $user = $this->userService->getEntityFromInterface($user);

        $agency = $user->getAdminAgency();

        if (null === $agency) {
            throw new Exception(sprintf('Logged in user %s is not the admin of an agency.', $user->getUsername()));
        }

        $branch = $this->branchFactory->createBranchEntityFromCreateBranchInputModel($createBranchInput, $agency);

        $this->entityManager->persist($branch);
        $this->entityManager->flush();

        $this->notificationService->sendBranchModerationNotification($branch);

        return new CreateBranchOutput(true);
    }

    public function findOrCreate(string $branchName, ?Agency $agency): Branch
    {
        $branch = $this->branchRepository->findOneBy(
            [
                'name' => $branchName,
                'agency' => $agency,
            ]
        );
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
