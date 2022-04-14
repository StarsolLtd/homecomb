<?php

namespace App\Repository\Locale;

use App\Entity\City;
use App\Entity\Locale\CityLocale;

interface CityLocaleRepositoryInterface
{
    public function findOneNullableByCity(City $city): ?CityLocale;

    public function findOneBySlug(string $slug): CityLocale;
}
