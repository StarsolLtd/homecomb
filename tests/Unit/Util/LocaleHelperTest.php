<?php

namespace App\Tests\Unit\Util;

use App\Entity\City;
use App\Entity\District;
use App\Entity\Locale\CityLocale;
use App\Entity\Locale\DistrictLocale;
use App\Entity\Locale\Locale;
use App\Util\LocaleHelper;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Util\LocaleHelper
 */
class LocaleHelperTest extends TestCase
{
    use ProphecyTrait;

    private LocaleHelper $localeHelper;

    public function setUp(): void
    {
        $this->localeHelper = new LocaleHelper();
    }

    /**
     * @covers \App\Util\LocaleHelper::generateSlug
     * Test generate a slug for a CityLocale.
     */
    public function testGenerateSlug1(): void
    {
        $city = $this->prophesize(City::class);
        $city->getCounty()->shouldBeCalledOnce()->willReturn('Cambridgeshire');
        $city->getCountryCode()->shouldBeCalledOnce()->willReturn('UK');

        $input = (new CityLocale())->setName('Ely')->setCity($city->reveal());

        $actual = $this->localeHelper->generateSlug(($input));

        $this->assertEquals('d51fe727e7a', $actual);
    }

    /**
     * @covers \App\Util\LocaleHelper::generateSlug
     * Test generate a slug for a DistrictLocale.
     */
    public function testGenerateSlug2(): void
    {
        $district = $this->prophesize(District::class);
        $district->getCounty()->shouldBeCalledOnce()->willReturn(null);
        $district->getCountryCode()->shouldBeCalledOnce()->willReturn('UK');

        $input = (new DistrictLocale())->setName('Hackney')->setDistrict($district->reveal());

        $actual = $this->localeHelper->generateSlug(($input));

        $this->assertEquals('f675a541c0f', $actual);
    }

    /**
     * @covers \App\Util\LocaleHelper::generateSlug
     * Test generate a slug for a Locale.
     */
    public function testGenerateSlug3(): void
    {
        $input = (new Locale())->setName('East Anglia');

        $actual = $this->localeHelper->generateSlug(($input));

        $this->assertEquals('9ad95b9b9d5', $actual);
    }
}
