<?php

namespace App\Tests\Unit\Entity;

use App\Entity\TenancyReviewSolicitation;

/**
 * @covers \App\Entity\TenancyReviewSolicitation
 */
class TenancyReviewSolicitationTest extends AbstractEntityTestCase
{
    protected function getEntity(): TenancyReviewSolicitation
    {
        return new TenancyReviewSolicitation();
    }
}
