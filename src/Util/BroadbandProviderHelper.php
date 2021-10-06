<?php

namespace App\Util;

use App\Entity\BroadbandProvider;
use App\Exception\DeveloperException;
use function md5;
use function substr;

class BroadbandProviderHelper
{
    public function generateSlug(BroadbandProvider $broadbandProvider): string
    {
        $name = $broadbandProvider->getName();
        if ('' === $name) {
            throw new DeveloperException('Unable to generate a slug for a BroadbandProvider without a name');
        }

        $string = $name.'/'.$broadbandProvider->getCountryCode();

        return substr(md5($string), 0, 10);
    }
}
