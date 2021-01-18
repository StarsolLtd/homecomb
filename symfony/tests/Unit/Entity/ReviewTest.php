<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Review;

/**
 * @covers \App\Entity\Review
 */
class ReviewTest extends AbstractEntityTestCase
{
    protected function getEntity(): Review
    {
        return new Review();
    }
}
