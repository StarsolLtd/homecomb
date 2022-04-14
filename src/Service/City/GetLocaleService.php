<?php

namespace App\Service\City;

use App\Repository\CityRepositoryInterface;
use App\Service\Locale\FindOrCreateService as LocaleFindOrCreateService;

class GetLocaleService
{
    public function __construct(
        private CityRepositoryInterface $cityRepository,
        private LocaleFindOrCreateService $localeFindOrCreateService,
    ) {
    }

    public function getLocaleSlugByCitySlug(string $citySlug): string
    {
        $city = $this->cityRepository->findOneBySlug($citySlug);

        $locale = $this->localeFindOrCreateService->findOrCreateByCity($city);

        return $locale->getSlug();
    }
}
