<?php

namespace App\Service;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Repository\AgencyRepository;
use App\Repository\BranchRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class BranchService
{
    private EntityManagerInterface $entityManager;
    private AgencyRepository $agencyRepository;
    private BranchRepository $branchRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        AgencyRepository $agencyRepository,
        BranchRepository $branchRepository
    ) {
        $this->entityManager = $entityManager;
        $this->agencyRepository = $agencyRepository;
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

        $branch = (new Branch())
            ->setAgency($agency)
            ->setName($branchName)
            ->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime());

        $this->entityManager->persist($branch);
        $this->entityManager->flush();

        return $branch;
    }
}
