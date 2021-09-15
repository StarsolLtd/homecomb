<?php

namespace App\Tests\Unit\Entity;

use App\Entity\District;

/**
 * @covers \App\Entity\District
 */
class DistrictTest extends AbstractEntityTestCase
{
    protected array $values = [
        'name' => 'North Norfolk',
        'county' => 'Norfolk',
        'countryCode' => 'UK',
        'type' => 'Borough',
        'published' => true,
        'slug' => 'test-district-slug',
    ];

    protected function getEntity(): District
    {
        $entity = (new District());
        $entity = $this->setPropertiesFromValuesArray($entity);
        assert($entity instanceof District);

        return $entity;
    }
}
