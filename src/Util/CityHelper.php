<?php

namespace App\Util;

use App\Entity\City;

class CityHelper
{
    public function generateSlug(City $city): string
    {
        $fields = implode('_', [
            $city->getName(),
            $city->getCounty(),
            $city->getCountryCode(),
        ]);

        return substr(md5($fields), 0, 16);
    }
}
