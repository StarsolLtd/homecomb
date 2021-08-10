<?php

namespace App\Tests\Unit\Entity;

use App\Entity\City;
use App\Entity\Locale\CityLocale;

/**
 * @covers \App\Entity\City
 */
class CityTest extends AbstractEntityTestCase
{
    public function testGetLocale1(): void
    {
        $this->assertEquals('test-city-locale-slug', $this->getEntity()->getLocale()->getSlug());
    }

    protected function getEntity(): City
    {
        $cityLocale = (new CityLocale())->setSlug('test-city-locale-slug');

        return (new City())->setLocale($cityLocale);
    }
}
