<?php

namespace App\Tests\Unit\Factory\Review;

use App\Entity\Locale\Locale;
use App\Entity\Review\LocaleReview;
use App\Factory\Review\LocaleReviewFactory;
use App\Model\Review\SubmitLocaleReviewInputInterface;
use App\Util\ReviewHelper;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Factory\Review\LocaleReviewFactory
 */
final class LocaleReviewFactoryTest extends TestCase
{
    use ProphecyTrait;

    private LocaleReviewFactory $localeReviewFactory;

    private ObjectProphecy $reviewHelper;

    public function setUp(): void
    {
        $this->reviewHelper = $this->prophesize(ReviewHelper::class);

        $this->localeReviewFactory = new LocaleReviewFactory($this->reviewHelper->reveal());
    }

    public function testCreateEntity1(): void
    {
        $input = $this->prophesize(SubmitLocaleReviewInputInterface::class);
        $input->getReviewerName()->shouldBeCalledOnce()->willReturn('John Smith');
        $input->getReviewTitle()->shouldBeCalledOnce()->willReturn('There is a market place');
        $input->getReviewContent()->shouldBeCalledOnce()->willReturn('I like living here, there is a Greggs.');
        $input->getOverallStars()->shouldBeCalledOnce()->willReturn(4);

        $locale = $this->prophesize(Locale::class);

        $this->reviewHelper->generateSlug(Argument::type(LocaleReview::class))->shouldBeCalledOnce()->willReturn('testslug');

        $entity = $this->localeReviewFactory->createEntity($input->reveal(), $locale->reveal());

        $this->assertEquals($locale->reveal(), $entity->getLocale());
        $this->assertEquals('John Smith', $entity->getAuthor());
        $this->assertEquals('There is a market place', $entity->getTitle());
        $this->assertEquals('I like living here, there is a Greggs.', $entity->getContent());
        $this->assertEquals(4, $entity->getOverallStars());
        $this->assertFalse($entity->isPublished());
        $this->assertCount(0, $entity->getVotes());
    }

    public function testCreateViewFromEntity1(): void
    {
        $localeReview = $this->prophesize(LocaleReview::class);
        $localeReview->getId()->shouldBeCalledOnce()->willReturn(125);
        $localeReview->getSlug()->shouldBeCalledOnce()->willReturn('test-slug');
        $localeReview->getAuthor()->shouldBeCalledOnce()->willReturn('John Smith');
        $localeReview->getTitle()->shouldBeCalledOnce()->willReturn('There is a market place');
        $localeReview->getContent()->shouldBeCalledOnce()->willReturn('I like living here, there is a Greggs.');
        $localeReview->getOverallStars()->shouldBeCalledOnce()->willReturn(4);
        $localeReview->getCreatedAt()->shouldBeCalledOnce()->willReturn(new \DateTime('2020-02-02 12:00:00'));
        $localeReview->getPositiveVotesCount()->shouldBeCalledOnce()->willReturn(1);
        $localeReview->getNegativeVotesCount()->shouldBeCalledOnce()->willReturn(0);
        $localeReview->getVotesScore()->shouldBeCalledOnce()->willReturn(1);

        $view = $this->localeReviewFactory->createViewFromEntity($localeReview->reveal());

        $this->assertEquals(125, $view->getId());
        $this->assertEquals('test-slug', $view->getSlug());
        $this->assertEquals('John Smith', $view->getAuthor());
        $this->assertEquals('There is a market place', $view->getTitle());
        $this->assertEquals('I like living here, there is a Greggs.', $view->getContent());
        $this->assertEquals(4, $view->getOverallStars());
        $this->assertEquals('2020-02-02', $view->getCreatedAt()->format('Y-m-d'));
        $this->assertEquals(1, $view->getPositiveVotes());
        $this->assertEquals(0, $view->getNegativeVotes());
        $this->assertEquals(1, $view->getVotesScore());
    }
}
