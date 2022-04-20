<?php

namespace App\Util;

use App\Entity\Agency;
use App\Exception\DeveloperException;

class AgencyHelper
{
    public function generateSlug(Agency $agency): string
    {
        $agencyName = $agency->getName();
        if ('' === $agencyName) {
            throw new DeveloperException('Unable to generate a slug for a Agency without a name');
        }

        return substr(md5($agencyName), 0, 14);
    }
}
