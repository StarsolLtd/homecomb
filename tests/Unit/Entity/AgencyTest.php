<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Agency;

/**
 * @covers \App\Entity\Agency
 */
final class AgencyTest extends AbstractEntityTestCase
{
    protected array $values = [
        'name' => 'Agency Name',
        'postcode' => 'EC1V 4LY',
        'countryCode' => 'UK',
        'externalUrl' => 'https://agency.test',
        'slug' => 'test-agency-slug',
        'published' => true,
    ];

    protected function getEntity(): Agency
    {
        $entity = new Agency();
        $entity = $this->setPropertiesFromValuesArray($entity);
        assert($entity instanceof Agency);

        return $entity;
    }
}
