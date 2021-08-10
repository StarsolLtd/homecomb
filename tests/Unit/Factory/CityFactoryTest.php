<?php

namespace App\Tests\Unit\Factory;

use App\Entity\City;
use App\Entity\Locale\CityLocale;
use App\Entity\Review\LocaleReview;
use App\Factory\CityFactory;
use App\Factory\Review\LocaleReviewFactory;
use App\Model\Review\LocaleReviewView;
use App\Util\CityHelper;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Factory\CityFactory
 */
class CityFactoryTest extends TestCase
{
    use ProphecyTrait;

    private CityFactory $cityFactory;

    private $cityHelper;
    private $localeReviewFactory;

    public function setUp(): void
    {
        $this->cityHelper = $this->prophesize(CityHelper::class);
        $this->localeReviewFactory = $this->prophesize(LocaleReviewFactory::class);

        $this->cityFactory = new CityFactory(
            $this->cityHelper->reveal(),
            $this->localeReviewFactory->reveal()
        );
    }

    /**
     * @covers \App\Factory\CityFactory::createEntity
     */
    public function testCreateEntity1(): void
    {
        $this->cityHelper->generateSlug(Argument::type(City::class))
            ->shouldBeCalledOnce()
            ->willReturn('test-city-slug');

        $city = $this->cityFactory->createEntity('Coventry', 'Warwickshire', 'UK');

        $this->assertEquals('Coventry', $city->getName());
        $this->assertEquals('Warwickshire', $city->getCounty());
        $this->assertEquals('UK', $city->getCountryCode());
        $this->assertEquals('test-city-slug', $city->getSlug());
        $this->assertTrue($city->isPublished());
    }

    /**
     * @covers \App\Factory\CityFactory::createModelFromEntity
     */
    public function testCreateModelFromEntity1(): void
    {
        $localeReview1 = $this->prophesize(LocaleReview::class);
        $localeReview2 = $this->prophesize(LocaleReview::class);

        $collection = (new ArrayCollection());
        $collection->add($localeReview1->reveal());
        $collection->add($localeReview2->reveal());

        $locale = $this->prophesize(CityLocale::class);
        $locale->getPublishedReviews()->shouldBeCalledOnce()->willReturn($collection);

        $entity = $this->prophesize(City::class);
        $entity->getName()->shouldBeCalledOnce()->willReturn('Coventry');
        $entity->getCounty()->shouldBeCalledOnce()->willReturn('Warwickshire');
        $entity->getCountryCode()->shouldBeCalledOnce()->willReturn('UK');
        $entity->getSlug()->shouldBeCalledOnce()->willReturn('test-city-slug');
        $entity->getLocale()->shouldBeCalledOnce()->willReturn($locale);

        $localeReviewView1 = $this->prophesize(LocaleReviewView::class);
        $this->localeReviewFactory->createViewFromEntity($localeReview1)->shouldBeCalledOnce()->willReturn($localeReviewView1);
        $localeReviewView2 = $this->prophesize(LocaleReviewView::class);
        $this->localeReviewFactory->createViewFromEntity($localeReview2)->shouldBeCalledOnce()->willReturn($localeReviewView2);

        $output = $this->cityFactory->createModelFromEntity($entity->reveal());

        $this->assertEquals('Coventry', $output->getName());
        $this->assertEquals('Warwickshire', $output->getCounty());
        $this->assertEquals('UK', $output->getCountryCode());
        $this->assertEquals('test-city-slug', $output->getSlug());
        $this->assertCount(2, $output->getLocaleReviews());
    }
}
