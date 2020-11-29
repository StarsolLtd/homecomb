<?php

namespace App\Tests\Unit\Util;

use App\Entity\Property;
use App\Util\PropertyHelper;
use PHPUnit\Framework\TestCase;

class PropertyHelperTest extends TestCase
{
    private PropertyHelper $propertyHelper;

    public function setUp(): void
    {
        $this->propertyHelper = new PropertyHelper();
    }

    public function testGenerateSlug(): void
    {
        $input = (new Property())
            ->setVendorPropertyId('TEST1234');

        $actual = $this->propertyHelper->generateSlug(($input));

        $this->assertEquals('f88db19b61af', $actual);
    }
}
