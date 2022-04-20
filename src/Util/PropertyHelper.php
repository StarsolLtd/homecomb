<?php

namespace App\Util;

use App\Entity\Property;
use App\Exception\DeveloperException;

class PropertyHelper
{
    public function generateSlug(Property $property): string
    {
        $vendorPropertyId = $property->getVendorPropertyId();

        if (null === $vendorPropertyId) {
            throw new DeveloperException('Unable to generate a slug for a Property without a vendorPropertyId.');
        }

        return substr(md5($vendorPropertyId), 0, 12);
    }
}
