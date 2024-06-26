<?php

namespace App\Service\Branch;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Repository\BranchRepositoryInterface;
use App\Util\BranchHelper;
use Doctrine\ORM\EntityManagerInterface;

class FindOrCreateService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private BranchHelper $branchHelper,
        private BranchRepositoryInterface $branchRepository,
    ) {
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

        $branch->setSlug($this->branchHelper->generateSlug($branch));

        return $branch;
    }
}
