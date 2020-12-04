<?php

namespace App\Service;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Repository\AgencyRepository;
use App\Repository\BranchRepository;
use App\Util\BranchHelper;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class BranchService
{
    private EntityManagerInterface $entityManager;
    private AgencyRepository $agencyRepository;
    private BranchHelper $branchHelper;
    private BranchRepository $branchRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        AgencyRepository $agencyRepository,
        BranchHelper $branchHelper,
        BranchRepository $branchRepository
    ) {
        $this->entityManager = $entityManager;
        $this->agencyRepository = $agencyRepository;
        $this->branchHelper = $branchHelper;
        $this->branchRepository = $branchRepository;
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
            ->setName($branchName)
            ->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime());

        $this->branchHelper->generateSlug($branch);

        return $branch;
    }
}
