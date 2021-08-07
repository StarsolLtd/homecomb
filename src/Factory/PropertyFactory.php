<?php

namespace App\Factory;

use App\Entity\Property;
use App\Exception\DeveloperException;
use App\Model\Property\PostcodeProperties;
use App\Model\Property\VendorProperty;
use App\Model\Property\View;
use App\Service\CityService;
use App\Util\PropertyHelper;
use function json_decode;

class PropertyFactory
{
    private const COUNTRY_CODE = 'UK';

    private CityService $cityService;
    private PropertyHelper $propertyHelper;
    private FlatModelFactory $flatModelFactory;
    private TenancyReviewFactory $tenancyReviewFactory;

    public function __construct(
        CityService $cityService,
        PropertyHelper $propertyHelper,
        FlatModelFactory $flatModelFactory,
        TenancyReviewFactory $tenancyReviewFactory
    ) {
        $this->cityService = $cityService;
        $this->propertyHelper = $propertyHelper;
        $this->flatModelFactory = $flatModelFactory;
        $this->tenancyReviewFactory = $tenancyReviewFactory;
    }

    public function createEntityFromVendorPropertyModel(VendorProperty $vendorProperty): Property
    {
        $vendorPropertyId = $vendorProperty->getVendorPropertyId();
        if (null === $vendorPropertyId) {
            throw new DeveloperException('Unable to create a property entity without a vendor property ID.');
        }

        $addressCity = $vendorProperty->getCity();
        $addressCounty = $vendorProperty->getCounty();

        $property = (new Property())
            ->setAddressLine1($vendorProperty->getAddressLine1())
            ->setAddressLine2($vendorProperty->getAddressLine2())
            ->setAddressLine3($vendorProperty->getAddressLine3())
            ->setAddressLine4($vendorProperty->getAddressLine4())
            ->setLocality($vendorProperty->getLocality())
            ->setAddressCity($addressCity)
            ->setCounty($addressCounty)
            ->setPostcode($vendorProperty->getPostcode())
            ->setCountryCode(self::COUNTRY_CODE)
            ->setDistrict($vendorProperty->getDistrict())
            ->setThoroughfare($vendorProperty->getThoroughFare())
            ->setLatitude($vendorProperty->getLatitude())
            ->setLongitude($vendorProperty->getLongitude())
            ->setVendorPropertyId($vendorProperty->getVendorPropertyId());

        $this->propertyHelper->generateSlug($property);

        $city = $this->cityService->findOrCreate($addressCity, $addressCounty, self::COUNTRY_CODE);

        $property->setCity($city);

        return $property;
    }

    public function createViewFromEntity(Property $entity): View
    {
        $tenancyReviews = [];
        foreach ($entity->getPublishedTenancyReviews() as $tenancyReviewEntity) {
            $tenancyReviews[] = $this->tenancyReviewFactory->createViewFromEntity($tenancyReviewEntity);
        }

        $cityEntity = $entity->getCity();
        $city = null !== $cityEntity ? $this->flatModelFactory->getCityFlatModel($cityEntity) : null;

        return new View(
            $entity->getSlug(),
            $entity->getAddressLine1(),
            $entity->getLocality(),
            $entity->getCity(),
            $entity->getPostcode(),
            $tenancyReviews,
            $entity->getLatitude(),
            $entity->getLongitude(),
            $city
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
                $address['thoroughfare'],
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
