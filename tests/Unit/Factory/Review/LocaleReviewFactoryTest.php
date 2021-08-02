<?php

namespace App\Tests\Unit\Factory\Review;

use App\Entity\Locale;
use App\Entity\Review\LocaleReview;
use App\Factory\Review\LocaleReviewFactory;
use App\Model\Review\SubmitLocaleReviewInput;
use App\Util\ReviewHelper;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Factory\Review\LocaleReviewFactory
 */
class LocaleReviewFactoryTest extends TestCase
{
    use ProphecyTrait;

    private LocaleReviewFactory $localeReviewFactory;

    private $reviewHelper;

    public function setUp(): void
    {
        $this->reviewHelper = $this->prophesize(ReviewHelper::class);

        $this->localeReviewFactory = new LocaleReviewFactory($this->reviewHelper->reveal());
    }

    /**
     * @covers \App\Factory\Review\LocaleReviewFactory::createEntityFromSubmitInput
     */
    public function testCreateEntityFromSubmitInput1(): void
    {
        $input = new SubmitLocaleReviewInput(
            'fakenham',
            'testcode',
            'John Smith',
            'john.smith@starsol.co.uk',
            'There is a market place',
            'I like living here, there is a Greggs.',
            4,
            'sample',
        );

        $locale = $this->prophesize(Locale::class);

        $this->reviewHelper->generateSlug(Argument::type(LocaleReview::class))->shouldBeCalledOnce()->willReturn('testslug');

        $entity = $this->localeReviewFactory->createEntity($input, $locale->reveal());

        $this->assertEquals($locale->reveal(), $entity->getLocale());
        $this->assertEquals('John Smith', $entity->getAuthor());
        $this->assertEquals('There is a market place', $entity->getTitle());
        $this->assertEquals('I like living here, there is a Greggs.', $entity->getContent());
        $this->assertEquals(4, $entity->getOverallStars());
        $this->assertFalse($entity->isPublished());
        $this->assertCount(0, $entity->getVotes());
    }
}
