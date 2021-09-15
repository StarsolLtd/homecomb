<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Image;

/**
 * @covers \App\Entity\Image
 */
class ImageTest extends AbstractEntityTestCase
{
    protected array $values = [
        'description' => 'Test Description',
        'type' => 'Photo',
        'image' => 'test-image',
    ];

    protected function getEntity(): Image
    {
        $entity = new Image();
        $entity = $this->setPropertiesFromValuesArray($entity);
        assert($entity instanceof Image);

        return $entity;
    }
}
