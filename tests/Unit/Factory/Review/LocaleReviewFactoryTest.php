<?php

namespace App\Tests\Unit\Factory\Review;

use App\Entity\Locale\Locale;
use App\Entity\Review\LocaleReview;
use App\Entity\TenancyReview;
use App\Entity\Vote\LocaleReviewVote;
use App\Factory\Review\LocaleReviewFactory;
use App\Model\Review\SubmitLocaleReviewInput;
use App\Repository\TenancyReviewRepository;
use App\Tests\Unit\SetIdByReflectionTrait;
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
    use SetIdByReflectionTrait;

    private LocaleReviewFactory $localeReviewFactory;

    private $reviewHelper;
    private $tenancyReviewRepository;

    public function setUp(): void
    {
        $this->reviewHelper = $this->prophesize(ReviewHelper::class);
        $this->tenancyReviewRepository = $this->prophesize(TenancyReviewRepository::class);

        $this->localeReviewFactory = new LocaleReviewFactory(
            $this->reviewHelper->reveal(),
            $this->tenancyReviewRepository->reveal()
        );
    }

    /**
     * @covers \App\Factory\Review\LocaleReviewFactory::createEntityFromSubmitInput
     */
    public function testCreateEntityFromSubmitInput1(): void
    {
        $input = new SubmitLocaleReviewInput(
            'fakenham',
            'test-tr-slug',
            'John Smith',
            'john.smith@starsol.co.uk',
            'There is a market place',
            'I like living here, there is a Greggs.',
            4,
            'sample',
        );

        $locale = $this->prophesize(Locale::class);
        $tenancyReview = $this->prophesize(TenancyReview::class);

        $this->tenancyReviewRepository->findOneNullableBySlug('test-tr-slug')
            ->shouldBeCalledOnce()
            ->willReturn($tenancyReview);

        $this->reviewHelper->generateSlug(Argument::type(LocaleReview::class))->shouldBeCalledOnce()->willReturn('testslug');

        $entity = $this->localeReviewFactory->createEntity($input, $locale->reveal());

        $this->assertEquals($locale->reveal(), $entity->getLocale());
        $this->assertEquals('John Smith', $entity->getAuthor());
        $this->assertEquals('There is a market place', $entity->getTitle());
        $this->assertEquals('I like living here, there is a Greggs.', $entity->getContent());
        $this->assertEquals(4, $entity->getOverallStars());
        $this->assertFalse($entity->isPublished());
        $this->assertCount(0, $entity->getVotes());
        $this->assertEquals($tenancyReview->reveal(), $entity->getTenancyReview());
    }

    /**
     * @covers \App\Factory\Review\LocaleReviewFactory::createViewFromEntity
     */
    public function testCreateViewFromEntity1(): void
    {
        $positiveVote = (new LocaleReviewVote())->setPositive(true);

        $localeReview = (new LocaleReview())
            ->setSlug('test-slug')
            ->setAuthor('John Smith')
            ->setTitle('There is a market place')
            ->setContent('I like living here, there is a Greggs.')
            ->setOverallStars(4)
            ->setCreatedAt(new \DateTime('2020-02-02 12:00:00'))
            ->addVote($positiveVote)
        ;

        $this->setIdByReflection($localeReview, 125);

        $view = $this->localeReviewFactory->createViewFromEntity($localeReview);

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
