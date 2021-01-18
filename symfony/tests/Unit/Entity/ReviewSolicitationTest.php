<?php

namespace App\Tests\Unit\Entity;

use App\Entity\ReviewSolicitation;

/**
 * @covers \App\Entity\ReviewSolicitation
 */
class ReviewSolicitationTest extends AbstractEntityTestCase
{
    protected function getEntity(): ReviewSolicitation
    {
        return new ReviewSolicitation();
    }
}
