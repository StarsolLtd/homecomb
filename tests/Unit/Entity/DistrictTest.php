<?php

namespace App\Tests\Unit\Entity;

use App\Entity\District;

/**
 * @covers \App\Entity\District
 */
class DistrictTest extends AbstractEntityTestCase
{
    protected function getEntity(): District
    {
        return new District();
    }
}
