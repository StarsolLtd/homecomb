<?php

namespace App\Factory;

use App\Entity\Property;
use App\Model\Property\VendorProperty;
use App\Model\Property\View;
use App\Util\PropertyHelper;

class PropertyFactory
{
    private PropertyHelper $propertyHelper;
    private ReviewFactory $reviewFactory;

    public function __construct(
        PropertyHelper $propertyHelper,
        ReviewFactory $reviewFactory
    ) {
        $this->propertyHelper = $propertyHelper;
        $this->reviewFactory = $reviewFactory;
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

    public function createViewFromEntity(Property $entity): View
    {
        $reviews = [];
        foreach ($entity->getPublishedReviews() as $reviewEntity) {
            $reviews[] = $this->reviewFactory->createViewFromEntity($reviewEntity);
        }

        return new View(
            $entity->getSlug(),
            $entity->getAddressLine1(),
            $entity->getPostcode(),
            $reviews
        );
    }
}
