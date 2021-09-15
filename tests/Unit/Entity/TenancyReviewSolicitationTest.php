<?php

namespace App\Tests\Unit\Entity;

use App\Entity\TenancyReviewSolicitation;

/**
 * @covers \App\Entity\TenancyReviewSolicitation
 */
class TenancyReviewSolicitationTest extends AbstractEntityTestCase
{
    protected array $values = [
        'recipientTitle' => 'Ms',
        'recipientFirstName' => 'Gina',
        'recipientLastName' => 'Pavel',
        'recipientEmail' => 'gina@starsol.co.uk',
        'code' => 'test-code',
    ];

    protected function getEntity(): TenancyReviewSolicitation
    {
        $entity = new TenancyReviewSolicitation();

        $entity = $this->setPropertiesFromValuesArray($entity);
        assert($entity instanceof TenancyReviewSolicitation);

        return $entity;
    }
}
