<?php

namespace App\Tests\Unit\Entity;

use App\Entity\LocaleReview;
use App\Entity\Vote\LocaleReviewVote;

/**
 * @covers \App\Entity\LocaleReview
 */
class LocaleReviewTest extends AbstractEntityTestCase
{
    /**
     * @covers \App\Entity\LocaleReview::getVotesScore
     */
    public function testGetVotesScore1(): void
    {
        $positiveVote1 = (new LocaleReviewVote())->setPositive(true);
        $negativeVote1 = (new LocaleReviewVote())->setPositive(false);
        $negativeVote2 = (new LocaleReviewVote())->setPositive(false);
        $negativeVote3 = (new LocaleReviewVote())->setPositive(false);

        $localeReview = $this->getEntity()
            ->addVote($positiveVote1)
            ->addVote($negativeVote1)
            ->addVote($negativeVote2)
            ->addVote($negativeVote3)
        ;

        $this->assertEquals(-2, $localeReview->getVotesScore());
    }

    /**
     * @covers \App\Entity\LocaleReview::getPositiveVotesCount
     */
    public function testGetPositiveVotesCount1(): void
    {
        $positiveVote1 = (new LocaleReviewVote())->setPositive(true);
        $positiveVote2 = (new LocaleReviewVote())->setPositive(true);
        $positiveVote3 = (new LocaleReviewVote())->setPositive(true);
        $negativeVote1 = (new LocaleReviewVote())->setPositive(false);

        $localeReview = $this->getEntity()
            ->addVote($positiveVote1)
            ->addVote($positiveVote2)
            ->addVote($positiveVote3)
            ->addVote($negativeVote1)
        ;

        $this->assertEquals(3, $localeReview->getPositiveVotesCount());
    }

    /**
     * @covers \App\Entity\LocaleReview::getNegativeVotesCount
     */
    public function testGetNegativeVotesCount1(): void
    {
        $positiveVote1 = (new LocaleReviewVote())->setPositive(true);
        $negativeVote1 = (new LocaleReviewVote())->setPositive(false);
        $negativeVote2 = (new LocaleReviewVote())->setPositive(false);
        $negativeVote3 = (new LocaleReviewVote())->setPositive(false);

        $localeReview = $this->getEntity()
            ->addVote($positiveVote1)
            ->addVote($negativeVote1)
            ->addVote($negativeVote2)
            ->addVote($negativeVote3)
        ;

        $this->assertEquals(3, $localeReview->getNegativeVotesCount());
    }

    protected function getEntity(): LocaleReview
    {
        return new LocaleReview();
    }
}
