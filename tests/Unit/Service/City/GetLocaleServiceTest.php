<?php

namespace App\Tests\Unit\Service\City;

use App\Entity\City;
use App\Entity\Locale\CityLocale;
use App\Repository\CityRepositoryInterface;
use App\Service\City\GetLocaleService;
use App\Service\Locale\FindOrCreateService as LocaleFindOrCreateService;
use App\Tests\Unit\EntityManagerTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class GetLocaleServiceTest extends TestCase
{
    use EntityManagerTrait;
    use ProphecyTrait;

    private GetLocaleService $getLocaleService;

    private ObjectProphecy $cityRepository;
    private ObjectProphecy $localeFindOrCreateService;

    public function setUp(): void
    {
        $this->cityRepository = $this->prophesize(CityRepositoryInterface::class);
        $this->localeFindOrCreateService = $this->prophesize(LocaleFindOrCreateService::class);

        $this->getLocaleService = new GetLocaleService(
            $this->cityRepository->reveal(),
            $this->localeFindOrCreateService->reveal(),
        );
    }

    public function testGetLocaleSlugByCitySlug1(): void
    {
        $city = $this->prophesize(City::class);
        $cityLocale = $this->prophesize(CityLocale::class);

        $this->cityRepository->findOneBySlug('test-city-slug')->willReturn($city->reveal());
        $this->localeFindOrCreateService->findOrCreateByCity($city)->willReturn($cityLocale->reveal());
        $cityLocale->getSlug()->shouldBeCalledOnce()->willReturn('test-locale-slug');

        $output = $this->getLocaleService->getLocaleSlugByCitySlug('test-city-slug');

        $this->assertEquals('test-locale-slug', $output);
    }
}
