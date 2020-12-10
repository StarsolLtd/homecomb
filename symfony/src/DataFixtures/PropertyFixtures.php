<?php

namespace App\DataFixtures;

use App\Entity\Property;
use App\Util\PropertyHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PropertyFixtures extends Fixture
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

    public function load(ObjectManager $manager): void
    {
        $properties = [];

        $properties[] = (new Property())
            ->setVendorPropertyId(self::PROPERTY_249_VENDOR_PROPERTY_ID)
            ->setAddressLine1('249 Victoria Road')
            ->setCity('Cambridge')
            ->setPostcode('CB4 3LF');

        $properties[] = (new Property())
            ->setVendorPropertyId(self::PROPERTY_25_VENDOR_PROPERTY_ID)
            ->setAddressLine1('25 Bateman Street')
            ->setCity('Cambridge')
            ->setPostcode('CB2 1NB');

        $properties[] = (new Property())
            ->setVendorPropertyId(self::PROPERTY_44_VENDOR_PROPERTY_ID)
            ->setAddressLine1('44 Fanshawe Road')
            ->setCity('Cambridge')
            ->setPostcode('CB1 3QY');

        $properties[] = (new Property())
            ->setVendorPropertyId(self::PROPERTY_22_VENDOR_PROPERTY_ID)
            ->setAddressLine1('22 Mingle Lane')
            ->setAddressLine2('Great Shelford')
            ->setCity('Cambridge')
            ->setPostcode('CB22 5BG');

        $properties[] = (new Property())
            ->setVendorPropertyId(self::PROPERTY_1_VENDOR_PROPERTY_ID)
            ->setAddressLine1('1 Primrose Lane')
            ->setAddressLine2('Waterbeach')
            ->setCity('Cambridge')
            ->setPostcode('CB25 9JZ');

        $properties[] = (new Property())
            ->setVendorPropertyId(self::PROPERTY_17_VENDOR_PROPERTY_ID)
            ->setAddressLine1('17 Redgate Road')
            ->setAddressLine2('Girton')
            ->setCity('Cambridge')
            ->setPostcode('CB3 0PP');

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
