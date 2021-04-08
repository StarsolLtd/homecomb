<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Agency;

/**
 * @covers \App\Entity\Agency
 */
class AgencyTest extends AbstractEntityTestCase
{
    protected function getEntity(): Agency
    {
        return new Agency();
    }
}
