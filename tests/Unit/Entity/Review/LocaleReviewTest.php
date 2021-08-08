<?php

namespace App\Tests\Unit\Entity\Review;

use App\Entity\Review\LocaleReview;
use App\Entity\TenancyReview;
use App\Entity\Vote\LocaleReviewVote;
use App\Tests\Unit\Entity\AbstractEntityTestCase;

/**
 * @covers \App\Entity\SubmitLocaleReview
 */
class LocaleReviewTest extends AbstractEntityTestCase
{
    /**
     * @covers \App\Entity\Review\LocaleReview::getVotesScore
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
     * @covers \App\Entity\Review\LocaleReview::getPositiveVotesCount
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
     * @covers \App\Entity\Review\LocaleReview::getNegativeVotesCount
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

    public function testGetTenancyReview1(): void
    {
        $this->assertEquals('Test tenancy review title', $this->getEntity()->getTenancyReview()->getTitle());
    }

    protected function getEntity(): LocaleReview
    {
        $tenancyReview = (new TenancyReview())->setTitle('Test tenancy review title');

        return (new LocaleReview())
            ->setTenancyReview($tenancyReview);
    }
}
