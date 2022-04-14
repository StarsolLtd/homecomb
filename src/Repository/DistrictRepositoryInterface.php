<?php

namespace App\Repository;

use App\Entity\District;

interface DistrictRepositoryInterface
{
    public function findOneByUnique(string $district, ?string $county, string $countryCode): ?District;

    public function findOneBySlug(string $slug): District;
}
