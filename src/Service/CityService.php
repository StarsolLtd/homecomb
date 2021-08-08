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
    private LocaleService $localeService;

    public function __construct(
        EntityManagerInterface $entityManager,
        CityFactory $cityFactory,
        CityRepository $cityRepository,
        LocaleService $localeService
    ) {
        $this->entityManager = $entityManager;
        $this->cityFactory = $cityFactory;
        $this->cityRepository = $cityRepository;
        $this->localeService = $localeService;
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

    public function getLocaleSlugByCitySlug(string $citySlug): string
    {
        $city = $this->cityRepository->findOneBySlug($citySlug);

        $locale = $this->localeService->findOrCreateByCity($city);

        return $locale->getSlug();
    }
}
