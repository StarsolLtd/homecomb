<?php

namespace App\Factory;

use App\Entity\Property;
use App\Model\VendorProperty;
use App\Util\PropertyHelper;

class PropertyFactory
{
    private PropertyHelper $propertyHelper;

    public function __construct(
        PropertyHelper $propertyHelper
    ) {
        $this->propertyHelper = $propertyHelper;
    }

    public function createEntityFromVendorPropertyModel(VendorProperty $vendorProperty): Property
    {
        $property = (new Property())
            ->setAddressLine1($vendorProperty->getAddressLine1())
            ->setAddressLine2($vendorProperty->getAddressLine2())
            ->setAddressLine3($vendorProperty->getAddressLine3())
            ->setAddressLine4($vendorProperty->getAddressLine4())
            ->setLocality($vendorProperty->getLocality())
            ->setCity($vendorProperty->getCity())
            ->setCounty($vendorProperty->getCounty())
            ->setPostcode($vendorProperty->getPostcode())
            ->setCountryCode('UK')
            ->setLatitude($vendorProperty->getLatitude())
            ->setLongitude($vendorProperty->getLongitude())
            ->setVendorPropertyId($vendorProperty->getVendorPropertyId());

        $this->propertyHelper->generateSlug($property);

        return $property;
    }
}
