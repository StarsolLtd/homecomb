<?php

namespace App\Factory;

use App\Entity\District;
use App\Util\DistrictHelper;

class DistrictFactory
{
    private DistrictHelper $districtHelper;

    public function __construct(
        DistrictHelper $districtHelper,
    ) {
        $this->districtHelper = $districtHelper;
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
