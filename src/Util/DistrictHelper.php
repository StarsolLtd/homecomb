<?php

namespace App\Util;

use App\Entity\District;

class DistrictHelper
{
    public function generateSlug(District $district): string
    {
        $fields = implode('_', [
            $district->getName(),
            $district->getCounty(),
            $district->getCountryCode(),
        ]);

        return substr(md5($fields), 0, 16);
    }
}
