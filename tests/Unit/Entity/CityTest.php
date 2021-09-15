<?php

namespace App\Tests\Unit\Entity;

use App\Entity\City;
use App\Entity\Locale\CityLocale;

/**
 * @covers \App\Entity\City
 */
class CityTest extends AbstractEntityTestCase
{
    protected array $values = [
        'name' => 'Norwich',
        'county' => 'Norfolk',
        'countryCode' => 'UK',
        'slug' => 'test-city-slug',
        'published' => true,
    ];

    public function testGetLocale1(): void
    {
        $this->assertEquals('test-city-locale-slug', $this->getEntity()->getLocale()->getSlug());
    }

    protected function getEntity(): City
    {
        $cityLocale = (new CityLocale())->setSlug('test-city-locale-slug');

        $entity = (new City())->setLocale($cityLocale);
        $entity = $this->setPropertiesFromValuesArray($entity);
        assert($entity instanceof City);

        return $entity;
    }
}
