<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Image;

/**
 * @covers \App\Entity\Image
 */
class ImageTest extends AbstractEntityTestCase
{
    protected function getEntity(): Image
    {
        return new Image();
    }
}
