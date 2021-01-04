<?php

namespace App\Util;

use App\Entity\Property;
use App\Exception\DeveloperException;
use function md5;
use function substr;

class PropertyHelper
{
    public function generateSlug(Property $property): string
    {
        if (null === $property->getVendorPropertyId()) {
            throw new DeveloperException('Unable to generate a slug for a Property without a vendorPropertyId.');
        }
        $slug = substr(md5($property->getVendorPropertyId()), 0, 12);
        $property->setSlug($slug);

        return $slug;
    }
}
