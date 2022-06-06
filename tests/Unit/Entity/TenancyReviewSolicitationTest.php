<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Branch;
use App\Entity\Property;
use App\Entity\TenancyReview;
use App\Entity\TenancyReviewSolicitation;

final class TenancyReviewSolicitationTest extends AbstractEntityTestCase
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

    public function testGetBranch1(): void
    {
        $entity = $this->getEntity();
        $branch = new Branch();
        $entity->setBranch($branch);
        $this->assertEquals($branch, $entity->getBranch());
    }

    public function testGetProperty1(): void
    {
        $entity = $this->getEntity();
        $property = new Property();
        $entity->setProperty($property);
        $this->assertEquals($property, $entity->getProperty());
    }

    public function testGetTenancyReview1(): void
    {
        $entity = $this->getEntity();
        $tenancyReview = new TenancyReview();
        $entity->setTenancyReview($tenancyReview);
        $this->assertEquals($tenancyReview, $entity->getTenancyReview());
    }
}
