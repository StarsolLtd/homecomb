<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Property;
use App\Factory\PropertyFactory;
use App\Model\VendorProperty;
use App\Util\PropertyHelper;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class PropertyFactoryTest extends TestCase
{
    use ProphecyTrait;

    private PropertyFactory $propertyFactory;

    private $propertyHelper;

    public function setUp(): void
    {
        $this->propertyHelper = $this->prophesize(PropertyHelper::class);

        $this->propertyFactory = new PropertyFactory(
            $this->propertyHelper->reveal(),
        );
    }

    public function testCreatePropertyEntityFromVendorPropertyModel(): void
    {
        $vendorPropertyModel = new VendorProperty(
            789,
            '249 Victoria Road',
            '',
            '',
            '',
            'Arbury',
            'Cambridge',
            'Cambridgeshire',
            'Cambridge',
            'England',
            'CB4 3LF',
            52.10101,
            -0.47261,
            true
        );

        $this->propertyHelper->generateSlug(Argument::type(Property::class))
            ->shouldBeCalledOnce()
            ->willReturn('ccc5382816c1');

        $property = $this->propertyFactory->createEntityFromVendorPropertyModel($vendorPropertyModel);

        $this->assertEquals(789, $property->getVendorPropertyId());
        $this->assertEquals('249 Victoria Road', $property->getAddressLine1());
        $this->assertEquals('', $property->getAddressLine2());
        $this->assertEquals('', $property->getAddressLine3());
        $this->assertEquals('', $property->getAddressLine4());
        $this->assertEquals('Arbury', $property->getLocality());
        $this->assertEquals('Cambridge', $property->getCity());
        $this->assertEquals('Cambridgeshire', $property->getCounty());
        $this->assertEquals('CB4 3LF', $property->getPostcode());
        $this->assertEquals(52.10101, $property->getLatitude());
        $this->assertEquals(-0.47261, $property->getLongitude());
    }

    public function testCreateFlatModelFromEntity(): void
    {
        $property = (new Property())
            ->setSlug('propertyslug')
            ->setAddressLine1('28 Bateman Street')
            ->setPostcode('CB2 2TG');

        $model = $this->propertyFactory->createFlatModelFromEntity($property);

        $this->assertEquals('propertyslug', $model->getSlug());
        $this->assertEquals('28 Bateman Street', $model->getAddressLine1());
        $this->assertEquals('CB2 2TG', $model->getPostcode());
    }
}
