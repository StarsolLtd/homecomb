<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Property;
use App\Entity\Review;

/**
 * @covers \App\Entity\Property
 */
class PropertyTest extends AbstractEntityTestCase
{
    /**
     * @covers \App\Entity\Property::__toString
     */
    public function testToString1(): void
    {
        $entity = $this->getEntity()
            ->setAddressLine1('249 Victoria Road')
            ->setPostcode('CB4 3LF');

        $this->assertEquals('249 Victoria Road, CB4 3LF', (string) $entity);
    }

    /**
     * @covers \App\Entity\Property::getPublishedReviews
     */
    public function testGetPublishedReviews1(): void
    {
        $entity = $this->getEntity();
        $this->assertEmpty($entity->getPublishedReviews());

        $reviewA = (new Review())->setPublished(true)->setTitle('A');
        $entity->addReview($reviewA);
        $this->assertCount(1, $entity->getPublishedReviews());

        $reviewB = (new Review())->setPublished(false)->setTitle('B');
        $entity->addReview($reviewB);
        $this->assertCount(1, $entity->getPublishedReviews());

        $reviewC = (new Review())->setPublished(true)->setTitle('C');
        $entity->addReview($reviewC);
        $this->assertCount(2, $entity->getPublishedReviews());

        $entity->addReview($reviewC);
        $this->assertCount(2, $entity->getPublishedReviews());
    }

    protected function getEntity(): Property
    {
        return new Property();
    }
}
