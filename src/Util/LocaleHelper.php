<?php

namespace App\Util;

use App\Entity\Locale\CityLocale;
use App\Entity\Locale\DistrictLocale;
use App\Entity\Locale\Locale;

class LocaleHelper
{
    public function generateSlug(Locale $locale): string
    {
        $type = 'Locale';
        if ($locale instanceof CityLocale) {
            $type = 'City';
        } elseif ($locale instanceof DistrictLocale) {
            $type = 'District';
        }

        $fields = implode('_', [
            $locale->getName(),
            $type,
        ]);

        return substr(md5($fields), 0, 11);
    }
}
