<?php

namespace App\Util;

use App\Entity\Locale\CityLocale;
use App\Entity\Locale\DistrictLocale;
use App\Entity\Locale\Locale;

class LocaleHelper
{
    public function generateSlug(Locale $locale): string
    {
        $components = [$locale->getName()];

        $type = 'Locale';
        if ($locale instanceof CityLocale) {
            $type = 'City';
            $city = $locale->getCity();

            $components[] = $city->getCounty();
            $components[] = $city->getCountryCode();
        } elseif ($locale instanceof DistrictLocale) {
            $type = 'District';
            $district = $locale->getDistrict();

            $components[] = $district->getCounty();
            $components[] = $district->getCountryCode();
        }

        $components[] = $type;

        $fields = implode('_', $components);

        return substr(md5($fields), 0, 11);
    }
}
