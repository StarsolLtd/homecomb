<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Review;
use App\Entity\Vote\ReviewVote;

/**
 * @covers \App\Entity\Review
 */
class ReviewTest extends AbstractEntityTestCase
{
    /**
     * @covers \App\Entity\Review::getVotesScore
     */
    public function testGetVotesScore1(): void
    {
        $positiveVote1 = (new ReviewVote())->setPositive(true);
        $negativeVote1 = (new ReviewVote())->setPositive(false);
        $negativeVote2 = (new ReviewVote())->setPositive(false);
        $negativeVote3 = (new ReviewVote())->setPositive(false);

        $review = $this->getEntity()
            ->addVote($positiveVote1)
            ->addVote($negativeVote1)
            ->addVote($negativeVote2)
            ->addVote($negativeVote3)
        ;

        $this->assertEquals(-2, $review->getVotesScore());
    }

    /**
     * @covers \App\Entity\Review::getPositiveVotesCount
     */
    public function testGetPositiveVotesCount1(): void
    {
        $positiveVote1 = (new ReviewVote())->setPositive(true);
        $positiveVote2 = (new ReviewVote())->setPositive(true);
        $positiveVote3 = (new ReviewVote())->setPositive(true);
        $negativeVote1 = (new ReviewVote())->setPositive(false);

        $review = $this->getEntity()
            ->addVote($positiveVote1)
            ->addVote($positiveVote2)
            ->addVote($positiveVote3)
            ->addVote($negativeVote1)
        ;

        $this->assertEquals(3, $review->getPositiveVotesCount());
    }

    /**
     * @covers \App\Entity\Review::getNegativeVotesCount
     */
    public function testGetNegativeVotesCount1(): void
    {
        $positiveVote1 = (new ReviewVote())->setPositive(true);
        $negativeVote1 = (new ReviewVote())->setPositive(false);
        $negativeVote2 = (new ReviewVote())->setPositive(false);
        $negativeVote3 = (new ReviewVote())->setPositive(false);

        $review = $this->getEntity()
            ->addVote($positiveVote1)
            ->addVote($negativeVote1)
            ->addVote($negativeVote2)
            ->addVote($negativeVote3)
        ;

        $this->assertEquals(3, $review->getNegativeVotesCount());
    }

    protected function getEntity(): Review
    {
        return new Review();
    }
}
