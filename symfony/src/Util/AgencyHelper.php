<?php

namespace App\Util;

use App\Entity\Agency;
use LogicException;
use function md5;
use function substr;

class AgencyHelper
{
    public function generateSlug(Agency $agency): string
    {
        if (null === $agency->getName()) {
            throw new LogicException('Unable to generate a slug for a Agency without a name.');
        }
        $slug = substr(md5($agency->getName()), 0, 14);
        $agency->setSlug($slug);

        return $slug;
    }
}
