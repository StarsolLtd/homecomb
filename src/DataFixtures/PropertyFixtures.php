<?php

namespace App\DataFixtures;

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
        $properties = [];

        $properties[] = (new Property())
            ->setVendorPropertyId(self::PROPERTY_249_VENDOR_PROPERTY_ID)
            ->setAddressLine1('249 Victoria Road')
            ->setCity('Cambridge')
            ->setPostcode('CB4 3LF')
            ->setDistrict('Cambridge')
            ->setThoroughfare('Victoria Road')
            ->setLatitude(52.21507263)
            ->setLongitude(0.11237954);

        $properties[] = (new Property())
            ->setVendorPropertyId(self::PROPERTY_25_VENDOR_PROPERTY_ID)
            ->setAddressLine1('25 Bateman Street')
            ->setCity('Cambridge')
            ->setPostcode('CB2 1NB')
            ->setDistrict('Cambridge')
            ->setThoroughfare('Bateman Street')
            ->setLatitude(52.19556427)
            ->setLongitude(0.12813538);

        $properties[] = (new Property())
            ->setVendorPropertyId(self::PROPERTY_44_VENDOR_PROPERTY_ID)
            ->setAddressLine1('44 Fanshawe Road')
            ->setCity('Cambridge')
            ->setPostcode('CB1 3QY')
            ->setDistrict('Cambridge')
            ->setThoroughfare('Fanshawe Road')
            ->setLatitude(52.19140625)
            ->setLongitude(0.14332861);

        $properties[] = (new Property())
            ->setVendorPropertyId(self::PROPERTY_22_VENDOR_PROPERTY_ID)
            ->setAddressLine1('22 Mingle Lane')
            ->setLocality('Great Shelford')
            ->setCity('Cambridge')
            ->setPostcode('CB22 5BG')
            ->setDistrict('South Cambridgeshire')
            ->setThoroughfare('Mingle Lane')
            ->setLatitude(52.14856600)
            ->setLongitude(0.14268900);

        $properties[] = (new Property())
            ->setVendorPropertyId(self::PROPERTY_1_VENDOR_PROPERTY_ID)
            ->setAddressLine1('1 Primrose Lane')
            ->setLocality('Waterbeach')
            ->setCity('Cambridge')
            ->setPostcode('CB25 9JZ')
            ->setDistrict('South Cambridgeshire')
            ->setThoroughfare('Primrose Lane')
            ->setLatitude(52.26825500)
            ->setLongitude(0.18656500);

        $properties[] = (new Property())
            ->setVendorPropertyId(self::PROPERTY_17_VENDOR_PROPERTY_ID)
            ->setAddressLine1('17 Redgate Road')
            ->setAddressLine2('Girton')
            ->setCity('Cambridge')
            ->setPostcode('CB3 0PP')
            ->setDistrict('South Cambridgeshire')
            ->setThoroughfare('Redgate Road')
            ->setLatitude(52.23755646)
            ->setLongitude(0.08636630);

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
