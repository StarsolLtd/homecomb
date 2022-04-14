<?php

namespace App\Tests\Unit\Service;

use App\Entity\City;
use App\Entity\Locale\CityLocale;
use App\Repository\CityRepository;
use App\Service\CityService;
use App\Service\Locale\FindOrCreateService as LocaleFindOrCreateService;
use App\Tests\Unit\EntityManagerTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Service\CityService
 */
final class CityServiceTest extends TestCase
{
    use EntityManagerTrait;
    use ProphecyTrait;

    private CityService $cityService;

    private ObjectProphecy $cityRepository;
    private ObjectProphecy $localeFindOrCreateService;

    public function setUp(): void
    {
        $this->cityRepository = $this->prophesize(CityRepository::class);
        $this->localeFindOrCreateService = $this->prophesize(LocaleFindOrCreateService::class);

        $this->cityService = new CityService(
            $this->cityRepository->reveal(),
            $this->localeFindOrCreateService->reveal(),
        );
    }

    /**
     * @covers \App\Service\CityService::getLocaleSlugByCitySlug
     */
    public function testGetLocaleSlugByCitySlug1(): void
    {
        $city = $this->prophesize(City::class);
        $cityLocale = $this->prophesize(CityLocale::class);

        $this->cityRepository->findOneBySlug('test-city-slug')->willReturn($city->reveal());
        $this->localeFindOrCreateService->findOrCreateByCity($city)->willReturn($cityLocale->reveal());
        $cityLocale->getSlug()->shouldBeCalledOnce()->willReturn('test-locale-slug');

        $output = $this->cityService->getLocaleSlugByCitySlug('test-city-slug');

        $this->assertEquals('test-locale-slug', $output);
    }
}
