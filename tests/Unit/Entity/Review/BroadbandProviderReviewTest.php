<?php

namespace App\Tests\Unit\Entity\Review;

use App\Entity\Review\BroadbandProviderReview;
use App\Entity\Vote\BroadbandProviderReviewVote;
use App\Tests\Unit\Entity\AbstractEntityTestCase;

final class BroadbandProviderReviewTest extends AbstractEntityTestCase
{
    protected array $values = [
        'author' => 'Jack Parnell',
        'title' => 'Test Title',
        'content' => 'Test Content',
        'overallStars' => 4,
        'slug' => 'test-review-slug',
        'published' => true,
    ];

    public function testGetVotesScore1(): void
    {
        $positiveVote1 = (new BroadbandProviderReviewVote())->setPositive(true);
        $negativeVote1 = (new BroadbandProviderReviewVote())->setPositive(false);
        $negativeVote2 = (new BroadbandProviderReviewVote())->setPositive(false);
        $negativeVote3 = (new BroadbandProviderReviewVote())->setPositive(false);

        $BroadbandProviderReview = $this->getEntity()
            ->addVote($positiveVote1)
            ->addVote($negativeVote1)
            ->addVote($negativeVote2)
            ->addVote($negativeVote3)
        ;

        $this->assertEquals(-2, $BroadbandProviderReview->getVotesScore());
    }

    public function testGetPositiveVotesCount1(): void
    {
        $positiveVote1 = (new BroadbandProviderReviewVote())->setPositive(true);
        $positiveVote2 = (new BroadbandProviderReviewVote())->setPositive(true);
        $positiveVote3 = (new BroadbandProviderReviewVote())->setPositive(true);
        $negativeVote1 = (new BroadbandProviderReviewVote())->setPositive(false);

        $BroadbandProviderReview = $this->getEntity()
            ->addVote($positiveVote1)
            ->addVote($positiveVote2)
            ->addVote($positiveVote3)
            ->addVote($negativeVote1)
        ;

        $this->assertEquals(3, $BroadbandProviderReview->getPositiveVotesCount());
    }

    public function testGetNegativeVotesCount1(): void
    {
        $positiveVote1 = (new BroadbandProviderReviewVote())->setPositive(true);
        $negativeVote1 = (new BroadbandProviderReviewVote())->setPositive(false);
        $negativeVote2 = (new BroadbandProviderReviewVote())->setPositive(false);
        $negativeVote3 = (new BroadbandProviderReviewVote())->setPositive(false);

        $BroadbandProviderReview = $this->getEntity()
            ->addVote($positiveVote1)
            ->addVote($negativeVote1)
            ->addVote($negativeVote2)
            ->addVote($negativeVote3)
        ;

        $this->assertEquals(3, $BroadbandProviderReview->getNegativeVotesCount());
    }

    protected function getEntity(): BroadbandProviderReview
    {
        $entity = new BroadbandProviderReview();
        $entity = $this->setPropertiesFromValuesArray($entity);
        assert($entity instanceof BroadbandProviderReview);

        return $entity;
    }
}
