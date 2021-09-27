<?php

namespace App\Tests\Unit\Util;

use App\Entity\Property;
use App\Exception\DeveloperException;
use App\Util\PropertyHelper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Util\PropertyHelper
 */
final class PropertyHelperTest extends TestCase
{
    private PropertyHelper $propertyHelper;

    public function setUp(): void
    {
        $this->propertyHelper = new PropertyHelper();
    }

    /**
     * @covers \App\Util\PropertyHelper::generateSlug
     */
    public function testGenerateSlug1(): void
    {
        $input = (new Property())->setVendorPropertyId('TEST1234');

        $actual = $this->propertyHelper->generateSlug(($input));

        $this->assertEquals('f88db19b61af', $actual);
    }

    /**
     * @covers \App\Util\PropertyHelper::generateSlug
     * Test throws DeveloperException when the property has no vendorPropertyId.
     */
    public function testGenerateSlug2(): void
    {
        $input = new Property();

        $this->expectException(DeveloperException::class);

        $this->propertyHelper->generateSlug($input);
    }
}
