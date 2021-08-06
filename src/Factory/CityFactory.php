<?php

namespace App\Factory;

use App\Entity\City;

class CityFactory
{
    public function createEntity(string $name, string $county, string $countryCode): City
    {
        return (new City())
            ->setName($name)
            ->setCounty($county)
            ->setCountryCode($countryCode);
    }
}
