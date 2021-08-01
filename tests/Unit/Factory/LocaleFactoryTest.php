<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Locale;
use App\Entity\TenancyReview;
use App\Exception\DeveloperException;
use App\Factory\LocaleFactory;
use App\Factory\TenancyReviewFactory;
use App\Model\TenancyReview\View as ReviewView;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Factory\LocaleFactory
 */
class LocaleFactoryTest extends TestCase
{
    use ProphecyTrait;

    private LocaleFactory $localeFactory;

    private $tenancyReviewFactory;

    public function setUp(): void
    {
        $this->tenancyReviewFactory = $this->prophesize(TenancyReviewFactory::class);

        $this->localeFactory = new LocaleFactory(
            $this->tenancyReviewFactory->reveal()
        );
    }

    /**
     * @covers \App\Factory\LocaleFactory::createViewFromEntity
     */
    public function testCreateViewFromEntity1(): void
    {
        $tenancyReview = (new TenancyReview())->setPublished(true);

        $reviewView = $this->prophesize(ReviewView::class);

        $locale = (new Locale())
            ->setName('Penzance')
            ->setSlugForTest('penzance')
            ->setContent('There arrrrre some pirates here.')
            ->addTenancyReview($tenancyReview)
        ;

        $this->tenancyReviewFactory->createViewFromEntity($tenancyReview)
            ->shouldBeCalledOnce()
            ->willReturn($reviewView)
        ;

        $view = $this->localeFactory->createViewFromEntity($locale);

        $this->assertEquals('Penzance', $view->getName());
        $this->assertEquals('There arrrrre some pirates here.', $view->getContent());
        $this->assertCount(1, $view->getTenancyReviews());
    }

    /**
     * @covers \App\Factory\LocaleFactory::getAgencyReviewsSummary
     */
    public function testGetAgencyReviewsSummary1(): void
    {
        $locale = new Locale();

        $agency1 = (new Agency())->setName('Agency 1')->setPublished(true)->setSlug('ag1');
        $branch1 = (new Branch())->setAgency($agency1)->setPublished(true);
        $agency2 = (new Agency())->setName('Agency 2')->setPublished(true)->setSlug('ag2');
        $branch2 = (new Branch())->setAgency($agency2)->setPublished(true);
        $review1 = (new TenancyReview())->setBranch($branch1)->setPublished(true)->setAgencyStars(5);
        $review2 = (new TenancyReview())->setBranch($branch1)->setPublished(true)->setAgencyStars(2);
        $review3 = (new TenancyReview())->setBranch($branch2)->setPublished(true)->setAgencyStars(4);
        $review4 = (new TenancyReview())->setBranch($branch2)->setPublished(false)->setAgencyStars(1);
        $review5 = (new TenancyReview())->setBranch($branch2)->setPublished(true)->setAgencyStars(null);
        $locale->addTenancyReview($review1)->addTenancyReview($review2)->addTenancyReview($review3)->addTenancyReview($review4)->addTenancyReview($review5);

        $output = $this->localeFactory->getAgencyReviewsSummary($locale);

        // Agencies should be ordered by mean rating descending, so the higher rated Agency 2 should be first
        $this->assertEquals('Agency 2', $output->getAgencyReviewSummaries()[0]->getAgencyName());
        $this->assertEquals('ag2', $output->getAgencyReviewSummaries()[0]->getAgencySlug());
        $this->assertEquals(4, $output->getAgencyReviewSummaries()[0]->getMeanRating());
        $this->assertEquals(1, $output->getAgencyReviewSummaries()[0]->getRatedCount());
        $this->assertEquals(0, $output->getAgencyReviewSummaries()[0]->getFiveStarCount());
        $this->assertEquals(1, $output->getAgencyReviewSummaries()[0]->getFourStarCount());
        $this->assertEquals(0, $output->getAgencyReviewSummaries()[0]->getThreeStarCount());
        $this->assertEquals(0, $output->getAgencyReviewSummaries()[0]->getTwoStarCount());
        $this->assertEquals(0, $output->getAgencyReviewSummaries()[0]->getOneStarCount());
        $this->assertEquals(1, $output->getAgencyReviewSummaries()[0]->getUnratedCount());

        $this->assertEquals('Agency 1', $output->getAgencyReviewSummaries()[1]->getAgencyName());
        $this->assertEquals('ag1', $output->getAgencyReviewSummaries()[1]->getAgencySlug());
        $this->assertEquals(3.5, $output->getAgencyReviewSummaries()[1]->getMeanRating());
        $this->assertEquals(2, $output->getAgencyReviewSummaries()[1]->getRatedCount());
        $this->assertEquals(1, $output->getAgencyReviewSummaries()[1]->getFiveStarCount());
        $this->assertEquals(0, $output->getAgencyReviewSummaries()[1]->getFourStarCount());
        $this->assertEquals(0, $output->getAgencyReviewSummaries()[1]->getThreeStarCount());
        $this->assertEquals(1, $output->getAgencyReviewSummaries()[1]->getTwoStarCount());
        $this->assertEquals(0, $output->getAgencyReviewSummaries()[1]->getOneStarCount());
        $this->assertEquals(0, $output->getAgencyReviewSummaries()[1]->getUnratedCount());

        $this->assertEquals(3, $output->getTenancyReviewsCount());
        $this->assertEquals(2, $output->getAgenciesCount());
    }

    /**
     * @covers \App\Factory\LocaleFactory::getAgencyReviewsSummary
     * Test throws DeveloperException when review has no agency
     */
    public function testGetAgencyReviewsSummary2(): void
    {
        $tenancyReview = $this->prophesize(TenancyReview::class);

        $tenancyReview->getAgency()
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $tenancyReview->getId()
            ->shouldBeCalledOnce()
            ->willReturn(45);

        $locale = $this->prophesize(Locale::class);

        $publishedReviewsWithPublishedAgency = (new ArrayCollection());
        $publishedReviewsWithPublishedAgency->add($tenancyReview->reveal());

        $locale->getPublishedTenancyReviewsWithPublishedAgency()
            ->shouldBeCalledOnce()
            ->willReturn($publishedReviewsWithPublishedAgency);

        $this->expectException(DeveloperException::class);

        $this->localeFactory->getAgencyReviewsSummary($locale->reveal());
    }
}
