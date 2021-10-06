<?php

namespace App\Tests\Unit\Entity;

use App\Entity\BroadbandProvider;

final class BroadbandProviderTest extends AbstractEntityTestCase
{
    protected array $values = [
        'name' => 'Test Provider',
        'countryCode' => 'UK',
        'externalUrl' => 'https://test.test',
        'slug' => 'test-provider-slug',
        'published' => true,
    ];

    protected function getEntity(): BroadbandProvider
    {
        $entity = new BroadbandProvider();
        $entity = $this->setPropertiesFromValuesArray($entity);
        assert($entity instanceof BroadbandProvider);

        return $entity;
    }
}
