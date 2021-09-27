<?php

namespace App\Tests\Unit\Util;

use App\Entity\Review\LocaleReview;
use App\Entity\TenancyReview;
use App\Entity\User;
use App\Util\ReviewHelper;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Util\ReviewHelper
 */
final class ReviewHelperTest extends TestCase
{
    use ProphecyTrait;

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

    /**
     * @covers \App\Util\ReviewHelper::generateTenancyReviewSlug
     */
    public function testGenerateTenancyReviewSlug1(): void
    {
        $tenancyReview = $this->prophesize(TenancyReview::class);
        $user = $this->prophesize(User::class);
        $tenancyReview->getAuthor()->shouldBeCalledOnce()->willReturn('Jane Doe');
        $tenancyReview->getTitle()->shouldBeCalledOnce()->willReturn('I lived here');
        $tenancyReview->getContent()->shouldBeCalledOnce()->willReturn('This house is green');
        $tenancyReview->getUser()->shouldBeCalledOnce()->willReturn($user);
        $user->getId()->shouldBeCalledOnce()->willReturn(55);

        $actual = $this->reviewHelper->generateTenancyReviewSlug($tenancyReview->reveal());

        $this->assertEquals('b65a7d1042eabb6', $actual);
    }
}
