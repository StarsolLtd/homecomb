<?php

namespace App\Tests\Unit\Service;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\City;
use App\Entity\District;
use App\Entity\Locale\CityLocale;
use App\Entity\Locale\DistrictLocale;
use App\Entity\Locale\Locale;
use App\Entity\TenancyReview;
use App\Factory\LocaleFactory;
use App\Model\Locale\View;
use App\Repository\Locale\CityLocaleRepository;
use App\Repository\Locale\DistrictLocaleRepository;
use App\Repository\Locale\LocaleRepository;
use App\Service\LocaleService;
use App\Tests\Unit\EntityManagerTrait;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Service\LocaleService
 */
class LocaleServiceTest extends TestCase
{
    use ProphecyTrait;
    use EntityManagerTrait;

    private LocaleService $localeService;

    private $entityManager;
    private $localeFactory;
    private $localeRepository;
    private $cityLocaleRepository;
    private $districtLocaleRepository;

    public function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManager::class);
        $this->localeFactory = $this->prophesize(LocaleFactory::class);
        $this->localeRepository = $this->prophesize(LocaleRepository::class);
        $this->cityLocaleRepository = $this->prophesize(CityLocaleRepository::class);
        $this->districtLocaleRepository = $this->prophesize(DistrictLocaleRepository::class);

        $this->localeService = new LocaleService(
            $this->entityManager->reveal(),
            $this->localeFactory->reveal(),
            $this->localeRepository->reveal(),
            $this->cityLocaleRepository->reveal(),
            $this->districtLocaleRepository->reveal(),
        );
    }

    /**
     * @covers \App\Service\LocaleService::getViewBySlug
     */
    public function testGetViewBySlug(): void
    {
        $locale = (new Locale());
        $view = new View('localeslug', 'Alton');

        $this->localeRepository->findOnePublishedBySlug('localeslug')
            ->shouldBeCalledOnce()
            ->willReturn($locale);

        $this->localeFactory->createViewFromEntity($locale)
            ->shouldBeCalledOnce()
            ->willReturn($view);

        $output = $this->localeService->getViewBySlug('localeslug');

        $this->assertEquals($view, $output);
    }

    /**
     * @covers \App\Service\LocaleService::findOrCreateByCity
     * Test a pre-existing CityLocale is returned.
     */
    public function testFindOrCreateByCity1()
    {
        $city = $this->prophesize(City::class);
        $cityLocale = $this->prophesize(CityLocale::class);

        $this->cityLocaleRepository->findOneNullableByCity($city)->shouldBeCalledOnce()->willReturn($cityLocale);

        $this->assertEntityManagerUnused();

        $output = $this->localeService->findOrCreateByCity($city->reveal());

        $this->assertEquals($output, $cityLocale->reveal());
    }

    /**
     * @covers \App\Service\LocaleService::findOrCreateByCity
     * Test a CityLocale is created if one does not already exist.
     */
    public function testFindOrCreateByCity2()
    {
        $city = $this->prophesize(City::class);
        $cityLocale = $this->prophesize(CityLocale::class);

        $this->cityLocaleRepository->findOneNullableByCity($city)->shouldBeCalledOnce()->willReturn(null);

        $this->localeFactory->createCityLocaleEntity($city)->shouldBeCalledOnce()->willReturn($cityLocale);

        $this->assertEntitiesArePersistedAndFlush([$cityLocale]);

        $output = $this->localeService->findOrCreateByCity($city->reveal());

        $this->assertEquals($output, $cityLocale->reveal());
    }

    /**
     * @covers \App\Service\LocaleService::findOrCreateByDistrict
     * Test a pre-existing DistrictLocale is returned.
     */
    public function testFindOrCreateByDistrict1()
    {
        $district = $this->prophesize(District::class);
        $districtLocale = $this->prophesize(DistrictLocale::class);

        $this->districtLocaleRepository->findOneNullableByDistrict($district)->shouldBeCalledOnce()->willReturn($districtLocale);

        $this->assertEntityManagerUnused();

        $output = $this->localeService->findOrCreateByDistrict($district->reveal());

        $this->assertEquals($output, $districtLocale->reveal());
    }

    /**
     * @covers \App\Service\LocaleService::findOrCreateByDistrict
     * Test a DistrictLocale is created if one does not already exist.
     */
    public function testFindOrCreateByDistrict2()
    {
        $district = $this->prophesize(District::class);
        $districtLocale = $this->prophesize(DistrictLocale::class);

        $this->districtLocaleRepository->findOneNullableByDistrict($district)->shouldBeCalledOnce()->willReturn(null);

        $this->localeFactory->createDistrictLocaleEntity($district)->shouldBeCalledOnce()->willReturn($districtLocale);

        $this->assertEntitiesArePersistedAndFlush([$districtLocale]);

        $output = $this->localeService->findOrCreateByDistrict($district->reveal());

        $this->assertEquals($output, $districtLocale->reveal());
    }

    /**
     * @covers \App\Service\LocaleService::getAgencyReviewsSummary
     */
    public function testGetAgencyReviewsSummary(): void
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

        $output = $this->localeService->getAgencyReviewsSummary($locale);

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
}
