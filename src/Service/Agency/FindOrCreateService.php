<?php

namespace App\Service\Agency;

use App\Entity\Agency;
use App\Factory\AgencyFactory;
use App\Repository\AgencyRepository;
use Doctrine\ORM\EntityManagerInterface;

class FindOrCreateService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AgencyFactory $agencyFactory,
        private AgencyRepository $agencyRepository
    ) {
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

        $agency = $this->agencyFactory->createEntityByName($agencyName);

        $this->entityManager->persist($agency);
        $this->entityManager->flush();

        return $agency;
    }
}
