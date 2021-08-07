<?php

namespace App\Tests\Unit\Util;

use App\Entity\Review\LocaleReview;
use App\Util\ReviewHelper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Util\ReviewHelper
 */
class ReviewHelperTest extends TestCase
{
    private ReviewHelper $reviewHelper;

    public function setUp(): void
    {
        $this->reviewHelper = new ReviewHelper();
    }

    /**
     * @covers \App\Util\ReviewHelper::generateSlug
     */
    public function testGenerateSlug1(): void
    {
        $localeReview = (new LocaleReview())
            ->setRelatedEntityId(789)
            ->setAuthor('Sonic Hedgehog')
            ->setTitle('Living here is terrible')
            ->setContent('There are animals trapped inside robots and they are trying to kill me')
            ->setOverallStars(2)
        ;

        $actual = $this->reviewHelper->generateSlug($localeReview);

        $this->assertEquals('36903cd72b9fc4d', $actual);
    }
}
