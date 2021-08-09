<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\City;
use App\Entity\District;
use App\Entity\Locale\CityLocale;
use App\Entity\Locale\DistrictLocale;
use App\Entity\Locale\Locale;
use App\Entity\Review\LocaleReview;
use App\Entity\TenancyReview;
use App\Exception\DeveloperException;
use App\Factory\LocaleFactory;
use App\Factory\Review\LocaleReviewFactory;
use App\Factory\TenancyReviewFactory;
use App\Model\Review\LocaleReviewView;
use App\Model\TenancyReview\View as TenancyReviewView;
use App\Util\LocaleHelper;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Factory\LocaleFactory
 */
class LocaleFactoryTest extends TestCase
{
    use ProphecyTrait;

    private LocaleFactory $localeFactory;

    private $localeHelper;
    private $localeReviewFactory;
    private $tenancyReviewFactory;

    public function setUp(): void
    {
        $this->localeHelper = $this->prophesize(LocaleHelper::class);
        $this->localeReviewFactory = $this->prophesize(LocaleReviewFactory::class);
        $this->tenancyReviewFactory = $this->prophesize(TenancyReviewFactory::class);

        $this->localeFactory = new LocaleFactory(
            $this->localeHelper->reveal(),
            $this->localeReviewFactory->reveal(),
            $this->tenancyReviewFactory->reveal()
        );
    }

    /**
     * @covers \App\Factory\LocaleFactory::createViewFromEntity
     */
    public function testCreateViewFromEntity1(): void
    {
        $locale = (new Locale())
            ->setName('Penzance')
            ->setSlug('penzance')
            ->setContent('There arrrrre some pirates here.')
        ;

        $localeReview = $this->prophesize(LocaleReview::class);
        $localeReview->setLocale($locale)->shouldBeCalledOnce()->willReturn($localeReview);
        $localeReview->isPublished()->shouldBeCalledOnce()->willReturn(true);

        $locale->addReview($localeReview->reveal());

        $tenancyReview = (new TenancyReview())->setPublished(true);
        $locale->addTenancyReview($tenancyReview);

        $localeReviewView = $this->prophesize(LocaleReviewView::class);
        $tenancyReviewView = $this->prophesize(TenancyReviewView::class);

        $this->localeReviewFactory->createViewFromEntity($localeReview)
            ->shouldBeCalledOnce()
            ->willReturn($localeReviewView)
        ;

        $this->tenancyReviewFactory->createViewFromEntity($tenancyReview)
            ->shouldBeCalledOnce()
            ->willReturn($tenancyReviewView)
        ;

        $view = $this->localeFactory->createViewFromEntity($locale);

        $this->assertEquals('Penzance', $view->getName());
        $this->assertEquals('There arrrrre some pirates here.', $view->getContent());
        $this->assertCount(1, $view->getLocaleReviews());
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

    /**
     * @covers \App\Factory\LocaleFactory::createCityLocaleEntity
     */
    public function testCreateCityLocaleEntity1(): void
    {
        $city = $this->prophesize(City::class);

        $city->getName()->shouldBeCalledOnce()->willReturn('Ely');

        $this->localeHelper->generateSlug(Argument::type(CityLocale::class))
            ->shouldBeCalledOnce()
            ->willReturn('test-slug');

        $entity = $this->localeFactory->createCityLocaleEntity($city->reveal());

        $this->assertEquals('Ely', $entity->getName());
        $this->assertEquals('test-slug', $entity->getSlug());
        $this->assertTrue($entity->isPublished());
    }

    /**
     * @covers \App\Factory\LocaleFactory::createDistrictLocaleEntity
     */
    public function testCreateDistrictLocaleEntity1(): void
    {
        $district = $this->prophesize(District::class);

        $district->getName()->shouldBeCalledOnce()->willReturn('East Cambridgeshire');

        $this->localeHelper->generateSlug(Argument::type(DistrictLocale::class))
            ->shouldBeCalledOnce()
            ->willReturn('test-district-slug');

        $entity = $this->localeFactory->createDistrictLocaleEntity($district->reveal());

        $this->assertEquals('East Cambridgeshire', $entity->getName());
        $this->assertEquals('test-district-slug', $entity->getSlug());
        $this->assertTrue($entity->isPublished());
    }
}
