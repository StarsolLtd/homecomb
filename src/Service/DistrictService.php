<?php

namespace App\Service;

use App\Repository\DistrictRepositoryInterface;
use App\Service\Locale\FindOrCreateService as LocaleFindOrCreateService;

class DistrictService
{
    public function __construct(
        private DistrictRepositoryInterface $districtRepository,
        private LocaleFindOrCreateService $localeFindOrCreateService
    ) {
    }

    public function getLocaleSlugByDistrictSlug(string $districtSlug): string
    {
        $district = $this->districtRepository->findOneBySlug($districtSlug);

        $locale = $this->localeFindOrCreateService->findOrCreateByDistrict($district);

        return $locale->getSlug();
    }
}
