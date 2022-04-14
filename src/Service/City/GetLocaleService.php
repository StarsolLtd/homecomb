<?php

namespace App\Service\City;

use App\Repository\CityRepository;
use App\Service\Locale\FindOrCreateService as LocaleFindOrCreateService;

class GetLocaleService
{
    public function __construct(
        private CityRepository $cityRepository,
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
