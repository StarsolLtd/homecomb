<?php

namespace App\Tests\Unit\Util;

use App\Entity\BroadbandProviderReview;
use App\Entity\User;
use App\Util\BroadbandProviderReviewHelper;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class BroadbandProviderReviewHelperTest extends TestCase
{
    use ProphecyTrait;

    private BroadbandProviderReviewHelper $broadbandProviderReviewHelper;

    public function setUp(): void
    {
        $this->broadbandProviderReviewHelper = new BroadbandProviderReviewHelper();
    }

    public function testGenerateSlug1(): void
    {
        $review = $this->prophesize(BroadbandProviderReview::class);
        $user = $this->prophesize(User::class);
        $review->getAuthor()->shouldBeCalledOnce()->willReturn('Sally Salmon');
        $review->getTitle()->shouldBeCalledOnce()->willReturn('It is fast');
        $review->getContent()->shouldBeCalledOnce()->willReturn('I downloaded a game');
        $review->getUser()->shouldBeCalledOnce()->willReturn($user);
        $user->getId()->shouldBeCalledOnce()->willReturn(77);

        $actual = $this->broadbandProviderReviewHelper->generateSlug($review->reveal());

        $this->assertEquals('abb208314526016', $actual);
    }
}
