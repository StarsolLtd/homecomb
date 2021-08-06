<?php

namespace App\Service;

use App\Entity\City;
use App\Factory\CityFactory;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;

class CityService
{
    private EntityManagerInterface $entityManager;
    private CityFactory $cityFactory;
    private CityRepository $cityRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        CityFactory $cityFactory,
        CityRepository $cityRepository
    ) {
        $this->entityManager = $entityManager;
        $this->cityFactory = $cityFactory;
        $this->cityRepository = $cityRepository;
    }

    public function findOrCreate(string $cityName, string $county, string $countryCode = 'UK'): City
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
