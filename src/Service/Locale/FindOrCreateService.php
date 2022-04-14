<?php

namespace App\Service\Locale;

use App\Entity\City;
use App\Entity\District;
use App\Entity\Locale\CityLocale;
use App\Entity\Locale\DistrictLocale;
use App\Factory\LocaleFactory;
use App\Repository\Locale\CityLocaleRepositoryInterface;
use App\Repository\Locale\DistrictLocaleRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class FindOrCreateService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private LocaleFactory $localeFactory,
        private CityLocaleRepositoryInterface $cityLocaleRepository,
        private DistrictLocaleRepositoryInterface $districtLocaleRepository
    ) {
    }

    public function findOrCreateByCity(City $city): CityLocale
    {
        $cityLocale = $this->cityLocaleRepository->findOneNullableByCity($city);

        if (null !== $cityLocale) {
            return $cityLocale;
        }

        $cityLocale = $this->localeFactory->createCityLocaleEntity($city);

        $this->entityManager->persist($cityLocale);
        $this->entityManager->flush();

        return $cityLocale;
    }

    public function findOrCreateByDistrict(District $district): DistrictLocale
    {
        $districtLocale = $this->districtLocaleRepository->findOneNullableByDistrict($district);

        if (null !== $districtLocale) {
            return $districtLocale;
        }

        $districtLocale = $this->localeFactory->createDistrictLocaleEntity($district);

        $this->entityManager->persist($districtLocale);
        $this->entityManager->flush();

        return $districtLocale;
    }
}
