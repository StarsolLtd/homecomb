<?php

namespace App\Service;

use App\Entity\District;
use App\Factory\DistrictFactory;
use App\Repository\DistrictRepository;
use Doctrine\ORM\EntityManagerInterface;

class DistrictService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DistrictFactory $districtFactory,
        private DistrictRepository $districtRepository,
        private LocaleService $localeService
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

    public function getLocaleSlugByDistrictSlug(string $districtSlug): string
    {
        $district = $this->districtRepository->findOneBySlug($districtSlug);

        $locale = $this->localeService->findOrCreateByDistrict($district);

        return $locale->getSlug();
    }
}
