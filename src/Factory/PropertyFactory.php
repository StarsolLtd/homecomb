<?php

namespace App\Factory;

use App\Entity\Property;
use App\Exception\DeveloperException;
use App\Model\Property\PostcodeProperties;
use App\Model\Property\VendorProperty;
use App\Model\Property\View;
use App\Service\CityService;
use App\Service\DistrictService;
use App\Util\PropertyHelper;
use function json_decode;

class PropertyFactory
{
    private const COUNTRY_CODE = 'UK';

    public function __construct(
        private CityService $cityService,
        private DistrictService $districtService,
        private PropertyHelper $propertyHelper,
        private CityFactory $cityFactory,
        private FlatModelFactory $flatModelFactory,
        private TenancyReviewFactory $tenancyReviewFactory
    ) {
    }

    public function createEntityFromVendorPropertyModel(VendorProperty $vendorProperty): Property
    {
        $vendorPropertyId = $vendorProperty->getVendorPropertyId();
        if (null === $vendorPropertyId) {
            throw new DeveloperException('Unable to create a property entity without a vendor property ID.');
        }

        $addressCity = $vendorProperty->getCity();
        $addressDistrict = $vendorProperty->getDistrict();
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
            ->setAddressDistrict($addressDistrict)
            ->setThoroughfare($vendorProperty->getThoroughFare())
            ->setLatitude($vendorProperty->getLatitude())
            ->setLongitude($vendorProperty->getLongitude())
            ->setVendorPropertyId($vendorProperty->getVendorPropertyId());

        $this->propertyHelper->generateSlug($property);

        $city = $this->cityService->findOrCreate($addressCity, $addressCounty, self::COUNTRY_CODE);
        $property->setCity($city);

        if (null !== $addressDistrict) {
            $district = $this->districtService->findOrCreate($addressDistrict, $addressCounty, self::COUNTRY_CODE);
            $property->setDistrict($district);
        }

        return $property;
    }

    public function createViewFromEntity(Property $entity): View
    {
        $tenancyReviews = [];
        foreach ($entity->getPublishedTenancyReviews() as $tenancyReviewEntity) {
            $tenancyReviews[] = $this->tenancyReviewFactory->createViewFromEntity($tenancyReviewEntity);
        }

        $cityEntity = $entity->getCity();
        $city = null !== $cityEntity ? $this->cityFactory->createModelFromEntity($cityEntity) : null;

        $districtEntity = $entity->getDistrict();
        $district = null !== $districtEntity ? $this->flatModelFactory->getDistrictFlatModel($districtEntity) : null;

        return new View(
            $entity->getSlug(),
            $entity->getAddressLine1(),
            $entity->getLocality(),
            $entity->getCity(),
            $entity->getPostcode(),
            $tenancyReviews,
            $entity->getLatitude(),
            $entity->getLongitude(),
            $city,
            $district,
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
