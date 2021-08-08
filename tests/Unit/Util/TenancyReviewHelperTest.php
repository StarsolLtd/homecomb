<?php

namespace App\Tests\Unit\Util;

use App\Entity\Property;
use App\Entity\TenancyReview;
use App\Util\TenancyReviewHelper;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Util\TenancyReviewHelper
 */
class TenancyReviewHelperTest extends TestCase
{
    use ProphecyTrait;

    private TenancyReviewHelper $tenancyReviewHelper;

    public function setUp(): void
    {
        $this->tenancyReviewHelper = new TenancyReviewHelper();
    }

    /**
     * @covers \App\Util\TenancyReviewHelper::generateSlug
     */
    public function testGenerateSlug1(): void
    {
        $property = $this->prophesize(Property::class);

        $property->getId()->shouldBeCalledOnce()->willReturn(5678);

        $tenancyReview = (new TenancyReview())
            ->setProperty($property->reveal())
            ->setAuthor('Sonic Hedgehog')
            ->setTitle('Living here is terrible')
            ->setContent('There are animals trapped inside robots and they are trying to kill me')
            ->setOverallStars(2)
        ;

        $actual = $this->tenancyReviewHelper->generateSlug($tenancyReview);

        $this->assertEquals('f5b1f8e718ed977', $actual);
    }
}
