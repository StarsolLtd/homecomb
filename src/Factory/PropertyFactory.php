<?php

namespace App\Factory;

use App\Entity\Property;
use App\Exception\DeveloperException;
use App\Model\Property\PostcodeProperties;
use App\Model\Property\VendorProperty;
use App\Model\Property\View;
use App\Util\PropertyHelper;
use function json_decode;

class PropertyFactory
{
    private PropertyHelper $propertyHelper;
    private TenancyReviewFactory $tenancyReviewFactory;

    public function __construct(
        PropertyHelper $propertyHelper,
        TenancyReviewFactory $tenancyReviewFactory
    ) {
        $this->propertyHelper = $propertyHelper;
        $this->tenancyReviewFactory = $tenancyReviewFactory;
    }

    public function createEntityFromVendorPropertyModel(VendorProperty $vendorProperty): Property
    {
        $vendorPropertyId = $vendorProperty->getVendorPropertyId();
        if (null === $vendorPropertyId) {
            throw new DeveloperException('Unable to create a property entity without a vendor property ID.');
        }

        $property = (new Property())
            ->setAddressLine1($vendorProperty->getAddressLine1())
            ->setAddressLine2($vendorProperty->getAddressLine2())
            ->setAddressLine3($vendorProperty->getAddressLine3())
            ->setAddressLine4($vendorProperty->getAddressLine4())
            ->setLocality($vendorProperty->getLocality())
            ->setAddressCity($vendorProperty->getCity())
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
        $tenancyReviews = [];
        foreach ($entity->getPublishedTenancyReviews() as $tenancyReviewEntity) {
            $tenancyReviews[] = $this->tenancyReviewFactory->createViewFromEntity($tenancyReviewEntity);
        }

        return new View(
            $entity->getSlug(),
            $entity->getAddressLine1(),
            $entity->getPostcode(),
            $tenancyReviews,
            $entity->getLatitude(),
            $entity->getLongitude()
        );
    }

    public function createPostcodePropertiesFromFindResponseContent(string $responseContent): PostcodeProperties
    {
        $result = json_decode($responseContent, true);

        $postcode = $result['postcode'];
        $latitude = $result['latitude'];
        $longitude = $result['longitude'];

        $vendorProperties = [];

        foreach ($result['addresses'] as $address) {
            $vendorProperties[] = new VendorProperty(
                null,
                $address['line_1'],
                $address['line_2'],
                $address['line_3'],
                $address['line_4'],
                $address['locality'],
                $address['town_or_city'],
                $address['county'],
                $address['district'],
                $address['country'],
                $postcode,
                $latitude,
                $longitude,
                $address['residential'] ?? null
            );
        }

        return new PostcodeProperties($postcode, $vendorProperties);
    }
}
