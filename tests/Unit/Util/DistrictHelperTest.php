<?php

namespace App\Tests\Unit\Util;

use App\Entity\District;
use App\Util\DistrictHelper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Util\DistrictHelper
 */
final class DistrictHelperTest extends TestCase
{
    private DistrictHelper $districtHelper;

    public function setUp(): void
    {
        $this->districtHelper = new DistrictHelper();
    }

    /**
     * @covers \App\Util\DistrictHelper::generateSlug
     */
    public function testGenerateSlug1(): void
    {
        $input = (new District())->setName('East Cambridgeshire')->setCounty('Cambridgeshire')->setCountryCode('UK');

        $actual = $this->districtHelper->generateSlug(($input));

        $this->assertEquals('ee95d77892b05afb', $actual);
    }
}
