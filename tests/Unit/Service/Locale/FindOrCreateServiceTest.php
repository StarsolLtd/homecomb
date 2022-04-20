<?php

namespace App\Tests\Unit\Service\Locale;

use App\Entity\City;
use App\Entity\District;
use App\Entity\Locale\CityLocale;
use App\Entity\Locale\DistrictLocale;
use App\Factory\LocaleFactory;
use App\Repository\Locale\CityLocaleRepositoryInterface;
use App\Repository\Locale\DistrictLocaleRepositoryInterface;
use App\Service\Locale\FindOrCreateService;
use App\Tests\Unit\EntityManagerTrait;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class FindOrCreateServiceTest extends TestCase
{
    use ProphecyTrait;
    use EntityManagerTrait;

    private FindOrCreateService $findOrCreateService;

    private ObjectProphecy $localeFactory;
    private ObjectProphecy $cityLocaleRepository;
    private ObjectProphecy $districtLocaleRepository;

    public function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManager::class);
        $this->localeFactory = $this->prophesize(LocaleFactory::class);
        $this->cityLocaleRepository = $this->prophesize(CityLocaleRepositoryInterface::class);
        $this->districtLocaleRepository = $this->prophesize(DistrictLocaleRepositoryInterface::class);

        $this->findOrCreateService = new FindOrCreateService(
            $this->entityManager->reveal(),
            $this->localeFactory->reveal(),
            $this->cityLocaleRepository->reveal(),
            $this->districtLocaleRepository->reveal(),
        );
    }

    /**
     * Test a pre-existing CityLocale is returned.
     */
    public function testFindOrCreateByCity1(): void
    {
        $city = $this->prophesize(City::class);
        $cityLocale = $this->prophesize(CityLocale::class);

        $this->cityLocaleRepository->findOneNullableByCity($city)->shouldBeCalledOnce()->willReturn($cityLocale);

        $this->assertEntityManagerUnused();

        $output = $this->findOrCreateService->findOrCreateByCity($city->reveal());

        $this->assertEquals($output, $cityLocale->reveal());
    }

    /**
     * Test a CityLocale is created if one does not already exist.
     */
    public function testFindOrCreateByCity2(): void
    {
        $city = $this->prophesize(City::class);
        $cityLocale = $this->prophesize(CityLocale::class);

        $this->cityLocaleRepository->findOneNullableByCity($city)->shouldBeCalledOnce()->willReturn(null);

        $this->localeFactory->createCityLocaleEntity($city)->shouldBeCalledOnce()->willReturn($cityLocale);

        $this->assertEntitiesArePersistedAndFlush([$cityLocale]);

        $output = $this->findOrCreateService->findOrCreateByCity($city->reveal());

        $this->assertEquals($output, $cityLocale->reveal());
    }

    /**
     * Test a pre-existing DistrictLocale is returned.
     */
    public function testFindOrCreateByDistrict1(): void
    {
        $district = $this->prophesize(District::class);
        $districtLocale = $this->prophesize(DistrictLocale::class);

        $this->districtLocaleRepository->findOneNullableByDistrict($district)->shouldBeCalledOnce()->willReturn($districtLocale);

        $this->assertEntityManagerUnused();

        $output = $this->findOrCreateService->findOrCreateByDistrict($district->reveal());

        $this->assertEquals($output, $districtLocale->reveal());
    }

    /**
     * Test a DistrictLocale is created if one does not already exist.
     */
    public function testFindOrCreateByDistrict2(): void
    {
        $district = $this->prophesize(District::class);
        $districtLocale = $this->prophesize(DistrictLocale::class);

        $this->districtLocaleRepository->findOneNullableByDistrict($district)->shouldBeCalledOnce()->willReturn(null);

        $this->localeFactory->createDistrictLocaleEntity($district)->shouldBeCalledOnce()->willReturn($districtLocale);

        $this->assertEntitiesArePersistedAndFlush([$districtLocale]);

        $output = $this->findOrCreateService->findOrCreateByDistrict($district->reveal());

        $this->assertEquals($output, $districtLocale->reveal());
    }
}
