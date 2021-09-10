<?php

namespace App\Tests\Unit\Entity\Locale;

use App\Entity\Locale\Locale;
use App\Entity\Review\LocaleReview;
use App\Entity\TenancyReview;
use App\Tests\Unit\Entity\AbstractEntityTestCase;

/**
 * @covers \App\Entity\Locale\Locale
 */
class LocaleTest extends AbstractEntityTestCase
{
    /**
     * @covers \App\Entity\Locale\Locale::getPublishedReviews
     */
    public function testGetPublishedReviews1(): void
    {
        $entity = $this->getEntity();
        $this->assertEmpty($entity->getPublishedReviews());

        $reviewA = (new LocaleReview())->setPublished(true)->setTitle('A');
        $entity->addReview($reviewA);
        $this->assertCount(1, $entity->getPublishedReviews());

        $reviewB = (new LocaleReview())->setPublished(false)->setTitle('B');
        $entity->addReview($reviewB);
        $this->assertCount(1, $entity->getPublishedReviews());

        $reviewC = (new LocaleReview())->setPublished(true)->setTitle('C');
        $entity->addReview($reviewC);
        $this->assertCount(2, $entity->getPublishedReviews());

        $entity->addReview($reviewC);
        $this->assertCount(2, $entity->getPublishedReviews());
    }

    /**
     * @covers \App\Entity\Locale\Locale::getPublishedTenancyReviews
     */
    public function testGetPublishedTenancyReviews1(): void
    {
        $entity = $this->getEntity();
        $this->assertEmpty($entity->getPublishedTenancyReviews());

        $reviewA = (new TenancyReview())->setPublished(true)->setTitle('A');
        $entity->addTenancyReview($reviewA);
        $this->assertCount(1, $entity->getPublishedTenancyReviews());

        $reviewB = (new TenancyReview())->setPublished(false)->setTitle('B');
        $entity->addTenancyReview($reviewB);
        $this->assertCount(1, $entity->getPublishedTenancyReviews());

        $reviewC = (new TenancyReview())->setPublished(true)->setTitle('C');
        $entity->addTenancyReview($reviewC);
        $this->assertCount(2, $entity->getPublishedTenancyReviews());

        $entity->addTenancyReview($reviewC);
        $this->assertCount(2, $entity->getPublishedTenancyReviews());
    }

    protected function getEntity(): Locale
    {
        return new Locale();
    }
}
