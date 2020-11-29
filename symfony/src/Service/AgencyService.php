<?php

namespace App\Service;

use App\Entity\Agency;
use App\Repository\AgencyRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class AgencyService
{
    private EntityManagerInterface $entityManager;
    private AgencyRepository $agencyRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        AgencyRepository $agencyRepository
    ) {
        $this->entityManager = $entityManager;
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

        $agency = (new Agency())
            ->setName($agencyName)
            ->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime());

        $this->entityManager->persist($agency);
        $this->entityManager->flush();

        return $agency;
    }
}
