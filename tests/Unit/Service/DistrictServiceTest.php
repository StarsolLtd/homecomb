<?php

namespace App\Tests\Unit\Service;

use App\Entity\District;
use App\Entity\Locale\DistrictLocale;
use App\Repository\DistrictRepositoryInterface;
use App\Service\DistrictService;
use App\Service\Locale\FindOrCreateService as LocaleFindOrCreateService;
use App\Tests\Unit\EntityManagerTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class DistrictServiceTest extends TestCase
{
    use EntityManagerTrait;
    use ProphecyTrait;

    private DistrictService $districtService;

    private ObjectProphecy $districtRepository;
    private ObjectProphecy $localeFindOrCreateService;

    public function setUp(): void
    {
        $this->districtRepository = $this->prophesize(DistrictRepositoryInterface::class);
        $this->localeFindOrCreateService = $this->prophesize(LocaleFindOrCreateService::class);

        $this->districtService = new DistrictService(
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

        $output = $this->districtService->getLocaleSlugByDistrictSlug('test-district-slug');

        $this->assertEquals('test-locale-slug', $output);
    }
}
