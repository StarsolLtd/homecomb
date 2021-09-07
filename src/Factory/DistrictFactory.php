<?php

namespace App\Factory;

use App\Entity\District;
use App\Util\DistrictHelper;

class DistrictFactory
{
    public function __construct(
        private DistrictHelper $districtHelper,
    ) {
    }

    public function createEntity(string $name, ?string $county, string $countryCode): District
    {
        $district = (new District())
            ->setName($name)
            ->setCounty($county)
            ->setCountryCode($countryCode);

        $district->setSlug($this->districtHelper->generateSlug($district));

        return $district;
    }
}
