<?php

namespace App\Factory;

use App\Entity\City;
use App\Util\CityHelper;

class CityFactory
{
    private CityHelper $cityHelper;

    public function __construct(
        CityHelper $cityHelper,
    ) {
        $this->cityHelper = $cityHelper;
    }

    public function createEntity(string $name, ?string $county, string $countryCode): City
    {
        $city = (new City())
            ->setName($name)
            ->setCounty($county)
            ->setCountryCode($countryCode);

        $city->setSlug($this->cityHelper->generateSlug($city));

        return $city;
    }
}
