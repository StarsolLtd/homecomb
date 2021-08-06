<?php

namespace App\Tests\Unit\Util;

use App\Entity\City;
use App\Util\CityHelper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Util\CityHelper
 */
class CityHelperTest extends TestCase
{
    private CityHelper $cityHelper;

    public function setUp(): void
    {
        $this->cityHelper = new CityHelper();
    }

    /**
     * @covers \App\Util\CityHelper::generateSlug
     */
    public function testGenerateSlug1(): void
    {
        $input = (new City())->setName('Ely')->setCounty('Cambridgeshire')->setCountryCode('UK');

        $actual = $this->cityHelper->generateSlug(($input));

        $this->assertEquals('96793e1af628ea1c', $actual);
    }
}
