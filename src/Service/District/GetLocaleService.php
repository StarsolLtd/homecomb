<?php

namespace App\Service\District;

use App\Repository\DistrictRepositoryInterface;
use App\Service\Locale\FindOrCreateService as LocaleFindOrCreateService;

class GetLocaleService
{
    public function __construct(
        private DistrictRepositoryInterface $districtRepository,
        private LocaleFindOrCreateService $localeFindOrCreateService,
    ) {
    }

    public function getLocaleSlugByDistrictSlug(string $districtSlug): string
    {
        $district = $this->districtRepository->findOneBySlug($districtSlug);

        return $this->localeFindOrCreateService->findOrCreateByDistrict($district)->getSlug();
    }
}
