<?php

namespace App\Repository;

use App\Entity\City;

interface CityRepositoryInterface
{
    public function findOneByUnique(string $city, ?string $county, string $countryCode): ?City;

    public function findOneBySlug(string $slug): City;
}
