<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Branch;

/**
 * @covers \App\Entity\Branch
 */
class BranchTest extends AbstractEntityTestCase
{
    protected function getEntity(): Branch
    {
        return new Branch();
    }
}
