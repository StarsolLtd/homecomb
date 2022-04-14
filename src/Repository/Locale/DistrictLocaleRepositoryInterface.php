<?php

namespace App\Repository\Locale;

use App\Entity\District;
use App\Entity\Locale\DistrictLocale;

interface DistrictLocaleRepositoryInterface
{
    public function findOneNullableByDistrict(District $district): ?DistrictLocale;
}
