<?php

namespace App\Tests\Unit\Util;

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
            'Arbury',
            null,
            'Cambridge',
            'CB4 3LF',
            52.10101,
            -0.47261
        );

        $this->propertyHelper->generateSlug(Argument::type(Property::class))
            ->shouldBeCalledOnce()
            ->willReturn('ccc5382816c1');

        $property = $this->propertyFactory->createPropertyEntityFromVendorPropertyModel($vendorPropertyModel);

        $this->assertEquals(789, $property->getVendorPropertyId());
        $this->assertEquals('249 Victoria Road', $property->getAddressLine1());
        $this->assertEquals('Arbury', $property->getAddressLine2());
        $this->assertNull($property->getAddressLine3());
        $this->assertEquals('Cambridge', $property->getCity());
        $this->assertEquals('CB4 3LF', $property->getPostcode());
        $this->assertEquals(52.10101, $property->getLatitude());
        $this->assertEquals(-0.47261, $property->getLongitude());
    }
}
