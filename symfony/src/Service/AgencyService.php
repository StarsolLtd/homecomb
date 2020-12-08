<?php

namespace App\Service;

use App\Entity\Agency;
use App\Repository\AgencyRepository;
use App\Util\AgencyHelper;
use Doctrine\ORM\EntityManagerInterface;

class AgencyService
{
    private EntityManagerInterface $entityManager;
    private AgencyHelper $agencyHelper;
    private AgencyRepository $agencyRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        AgencyHelper $agencyHelper,
        AgencyRepository $agencyRepository
    ) {
        $this->entityManager = $entityManager;
        $this->agencyHelper = $agencyHelper;
        $this->agencyRepository = $agencyRepository;
    }

    public function findOrCreateByName(string $agencyName): Agency
    {
        $agency = $this->agencyRepository->findOneBy(
            [
                'name' => $agencyName,
            ]
        );
        if (null !== $agency) {
            return $agency;
        }

        $agency = $this->create($agencyName);

        $this->entityManager->persist($agency);
        $this->entityManager->flush();

        return $agency;
    }

    private function create(string $agencyName): Agency
    {
        $agency = (new Agency())
            ->setName($agencyName);

        $this->agencyHelper->generateSlug($agency);

        return $agency;
    }
}
