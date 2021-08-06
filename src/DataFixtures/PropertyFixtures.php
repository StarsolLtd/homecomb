<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Property;
use App\Util\PropertyHelper;
use Doctrine\Persistence\ObjectManager;

class PropertyFixtures extends AbstractDataFixtures
{
    private PropertyHelper $propertyHelper;

    public const PROPERTY_1_VENDOR_PROPERTY_ID = 'ZGQ1NTAxMmE1YjA0YWRkIDE2OTEyOTQwIDMzZjhlNDFkNGU1MzY0Mw==';
    public const PROPERTY_17_VENDOR_PROPERTY_ID = 'NTg4ZWNkYjE0Y2FmNTJjIDE2OTQxNTcyIDMzZjhlNDFkNGU1MzY0Mw==';
    public const PROPERTY_22_VENDOR_PROPERTY_ID = 'NGViYmZiZjY5YjBiYTAyIDE2NjYxMjMzIDMzZjhlNDFkNGU1MzY0Mw==';
    public const PROPERTY_25_VENDOR_PROPERTY_ID = 'MjgwNjUwNjUwY2M3M2ViIDE2NTQ2NTY0IDMzZjhlNDFkNGU1MzY0Mw==';
    public const PROPERTY_44_VENDOR_PROPERTY_ID = 'OWM3ODAxYzFiYTEzMzE3IDE2MzkxMzg2IDMzZjhlNDFkNGU1MzY0Mw==';
    public const PROPERTY_249_VENDOR_PROPERTY_ID = 'ZmM5Yzc5MzMyODAyZTc4IDE3MDQ0OTcyIDMzZjhlNDFkNGU1MzY0Mw==';

    public function __construct(
        PropertyHelper $propertyHelper
    ) {
        $this->propertyHelper = $propertyHelper;
    }

    protected function getEnvironments(): array
    {
        return ['dev', 'prod'];
    }

    protected function doLoad(ObjectManager $manager): void
    {
        /** @var City $cambridge */
        $cambridge = $this->getReference('city-'.CityFixtures::CAMBRIDGE_SLUG);

        $properties = [];

        $properties[] = (new Property())
            ->setVendorPropertyId(self::PROPERTY_249_VENDOR_PROPERTY_ID)
            ->setAddressLine1('249 Victoria Road')
            ->setAddressCity('Cambridge')
            ->setPostcode('CB4 3LF')
            ->setLatitude(52.21507263)
            ->setLongitude(0.11237954)
            ->setCity($cambridge)
        ;

        $properties[] = (new Property())
            ->setVendorPropertyId(self::PROPERTY_25_VENDOR_PROPERTY_ID)
            ->setAddressLine1('25 Bateman Street')
            ->setAddressCity('Cambridge')
            ->setPostcode('CB2 1NB')
            ->setLatitude(52.19556427)
            ->setLongitude(0.12813538)
            ->setCity($cambridge)
        ;

        $properties[] = (new Property())
            ->setVendorPropertyId(self::PROPERTY_44_VENDOR_PROPERTY_ID)
            ->setAddressLine1('44 Fanshawe Road')
            ->setAddressCity('Cambridge')
            ->setPostcode('CB1 3QY')
            ->setLatitude(52.19140625)
            ->setLongitude(0.14332861)
            ->setCity($cambridge)
        ;

        $properties[] = (new Property())
            ->setVendorPropertyId(self::PROPERTY_22_VENDOR_PROPERTY_ID)
            ->setAddressLine1('22 Mingle Lane')
            ->setAddressLine2('Great Shelford')
            ->setAddressCity('Cambridge')
            ->setPostcode('CB22 5BG')
            ->setLatitude(52.14856600)
            ->setLongitude(0.14268900)
            ->setCity($cambridge)
        ;

        $properties[] = (new Property())
            ->setVendorPropertyId(self::PROPERTY_1_VENDOR_PROPERTY_ID)
            ->setAddressLine1('1 Primrose Lane')
            ->setAddressLine2('Waterbeach')
            ->setAddressCity('Cambridge')
            ->setPostcode('CB25 9JZ')
            ->setLatitude(52.26825500)
            ->setLongitude(0.18656500)
            ->setCity($cambridge)
        ;

        $properties[] = (new Property())
            ->setVendorPropertyId(self::PROPERTY_17_VENDOR_PROPERTY_ID)
            ->setAddressLine1('17 Redgate Road')
            ->setAddressLine2('Girton')
            ->setAddressCity('Cambridge')
            ->setPostcode('CB3 0PP')
            ->setLatitude(52.23755646)
            ->setLongitude(0.08636630)
            ->setCity($cambridge)
        ;

        foreach ($properties as $property) {
            $property->setPublished(true);
            $property->setCountryCode('UK');
            $this->propertyHelper->generateSlug($property);
            $manager->persist($property);

            $this->addReference('property-'.$property->getVendorPropertyId(), $property);
        }

        $manager->flush();
    }
}
