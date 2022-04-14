<?php

namespace App\Service\City;

use App\Entity\City;
use App\Factory\CityFactory;
use App\Repository\CityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class FindOrCreateService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CityFactory $cityFactory,
        private CityRepositoryInterface $cityRepository,
    ) {
    }

    public function findOrCreate(string $cityName, ?string $county, string $countryCode = 'UK'): City
    {
        $city = $this->cityRepository->findOneByUnique($cityName, $county, $countryCode);

        if (null !== $city) {
            return $city;
        }

        $city = $this->cityFactory->createEntity($cityName, $county, $countryCode);

        $this->entityManager->persist($city);
        $this->entityManager->flush();

        return $city;
    }
}
