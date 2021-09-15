<?php

namespace App\Tests\Unit\Entity;

use App\Entity\TenancyReview;
use App\Entity\Vote\TenancyReviewVote;

/**
 * @covers \App\Entity\TenancyReview
 */
class TenancyReviewTest extends AbstractEntityTestCase
{
    protected array $values = [
        'author' => 'Jack Parnell',
        'title' => 'Test Title',
        'content' => 'Test Content',
        'overallStars' => 4,
        'propertyStars' => 5,
        'agencyStars' => 3,
        'landlordStars' => 4,
        'published' => true,
        'slug' => 'test-slug',
    ];

    /**
     * @covers \App\Entity\TenancyReview::getVotesScore
     */
    public function testGetVotesScore1(): void
    {
        $positiveVote1 = (new TenancyReviewVote())->setPositive(true);
        $negativeVote1 = (new TenancyReviewVote())->setPositive(false);
        $negativeVote2 = (new TenancyReviewVote())->setPositive(false);
        $negativeVote3 = (new TenancyReviewVote())->setPositive(false);

        $tenancyReview = $this->getEntity()
            ->addVote($positiveVote1)
            ->addVote($negativeVote1)
            ->addVote($negativeVote2)
            ->addVote($negativeVote3)
        ;

        $this->assertEquals(-2, $tenancyReview->getVotesScore());
    }

    /**
     * @covers \App\Entity\TenancyReview::getPositiveVotesCount
     */
    public function testGetPositiveVotesCount1(): void
    {
        $positiveVote1 = (new TenancyReviewVote())->setPositive(true);
        $positiveVote2 = (new TenancyReviewVote())->setPositive(true);
        $positiveVote3 = (new TenancyReviewVote())->setPositive(true);
        $negativeVote1 = (new TenancyReviewVote())->setPositive(false);

        $tenancyReview = $this->getEntity()
            ->addVote($positiveVote1)
            ->addVote($positiveVote2)
            ->addVote($positiveVote3)
            ->addVote($negativeVote1)
        ;

        $this->assertEquals(3, $tenancyReview->getPositiveVotesCount());
    }

    /**
     * @covers \App\Entity\TenancyReview::getNegativeVotesCount
     */
    public function testGetNegativeVotesCount1(): void
    {
        $positiveVote1 = (new TenancyReviewVote())->setPositive(true);
        $negativeVote1 = (new TenancyReviewVote())->setPositive(false);
        $negativeVote2 = (new TenancyReviewVote())->setPositive(false);
        $negativeVote3 = (new TenancyReviewVote())->setPositive(false);

        $tenancyReview = $this->getEntity()
            ->addVote($positiveVote1)
            ->addVote($negativeVote1)
            ->addVote($negativeVote2)
            ->addVote($negativeVote3)
        ;

        $this->assertEquals(3, $tenancyReview->getNegativeVotesCount());
    }

    protected function getEntity(): TenancyReview
    {
        $entity = new TenancyReview();
        $entity = $this->setPropertiesFromValuesArray($entity);
        assert($entity instanceof TenancyReview);

        return $entity;
    }
}
