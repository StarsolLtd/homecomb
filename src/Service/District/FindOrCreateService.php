<?php

namespace App\Service\District;

use App\Entity\District;
use App\Factory\DistrictFactory;
use App\Repository\DistrictRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class FindOrCreateService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DistrictFactory $districtFactory,
        private DistrictRepositoryInterface $districtRepository,
    ) {
    }

    public function findOrCreate(string $districtName, ?string $county, string $countryCode = 'UK'): District
    {
        $district = $this->districtRepository->findOneByUnique($districtName, $county, $countryCode);

        if (null !== $district) {
            return $district;
        }

        $district = $this->districtFactory->createEntity($districtName, $county, $countryCode);

        $this->entityManager->persist($district);
        $this->entityManager->flush();

        return $district;
    }
}
