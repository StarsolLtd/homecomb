<?php

namespace App\Service\Agency;

use App\Entity\Agency;
use App\Repository\AgencyRepository;
use App\Util\AgencyHelper;
use Doctrine\ORM\EntityManagerInterface;

class FindOrCreateService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AgencyHelper $agencyHelper,
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

        $agency = (new Agency())
            ->setName($agencyName);

        $agency->setSlug($this->agencyHelper->generateSlug($agency));

        $this->entityManager->persist($agency);
        $this->entityManager->flush();

        return $agency;
    }
}
