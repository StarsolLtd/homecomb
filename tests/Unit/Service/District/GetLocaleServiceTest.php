<?php

namespace App\Tests\Unit\Service\District;

use App\Entity\District;
use App\Entity\Locale\DistrictLocale;
use App\Repository\DistrictRepositoryInterface;
use App\Service\District\GetLocaleService;
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

    private ObjectProphecy $districtRepository;
    private ObjectProphecy $localeFindOrCreateService;

    public function setUp(): void
    {
        $this->districtRepository = $this->prophesize(DistrictRepositoryInterface::class);
        $this->localeFindOrCreateService = $this->prophesize(LocaleFindOrCreateService::class);

        $this->getLocaleService = new GetLocaleService(
            $this->districtRepository->reveal(),
            $this->localeFindOrCreateService->reveal(),
        );
    }

    public function testGetLocaleSlugByDistrictSlug1(): void
    {
        $district = $this->prophesize(District::class);
        $districtLocale = $this->prophesize(DistrictLocale::class);

        $this->districtRepository->findOneBySlug('test-district-slug')->willReturn($district->reveal());
        $this->localeFindOrCreateService->findOrCreateByDistrict($district)->willReturn($districtLocale->reveal());
        $districtLocale->getSlug()->shouldBeCalledOnce()->willReturn('test-locale-slug');

        $output = $this->getLocaleService->getLocaleSlugByDistrictSlug('test-district-slug');

        $this->assertEquals('test-locale-slug', $output);
    }
}
