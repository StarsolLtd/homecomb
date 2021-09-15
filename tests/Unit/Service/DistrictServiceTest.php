<?php

namespace App\Tests\Unit\Service;

use App\Entity\District;
use App\Entity\Locale\DistrictLocale;
use App\Factory\DistrictFactory;
use App\Repository\DistrictRepository;
use App\Service\DistrictService;
use App\Service\LocaleService;
use App\Tests\Unit\EntityManagerTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Service\DistrictService
 */
class DistrictServiceTest extends TestCase
{
    use EntityManagerTrait;
    use ProphecyTrait;

    private DistrictService $districtService;

    private ObjectProphecy $districtFactory;
    private ObjectProphecy $districtRepository;
    private ObjectProphecy $localeService;

    public function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->districtFactory = $this->prophesize(DistrictFactory::class);
        $this->districtRepository = $this->prophesize(DistrictRepository::class);
        $this->localeService = $this->prophesize(LocaleService::class);

        $this->districtService = new DistrictService(
            $this->entityManager->reveal(),
            $this->districtFactory->reveal(),
            $this->districtRepository->reveal(),
            $this->localeService->reveal(),
        );
    }

    /**
     * @covers \App\Service\DistrictService::findOrCreate
     * Test an entity is created and persisted when it does not already exist.
     */
    public function testFindOrCreate1(): void
    {
        $district = $this->prophesize(District::class);

        $this->districtRepository->findOneByUnique("King's Lynn And West Norfolk", 'Norfolk', 'UK')
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $this->districtFactory->createEntity("King's Lynn And West Norfolk", 'Norfolk', 'UK')
            ->shouldBeCalledOnce()
            ->willReturn($district);

        $output = $this->districtService->findOrCreate("King's Lynn And West Norfolk", 'Norfolk', 'UK');

        $this->assertEntitiesArePersistedAndFlush([$district]);

        $this->assertEquals($district->reveal(), $output);
    }

    /**
     * @covers \App\Service\DistrictService::findOrCreate
     * Test that if the repository finds a record, that entity is returned.
     */
    public function testFindOrCreate2(): void
    {
        $district = $this->prophesize(District::class);

        $this->districtRepository->findOneByUnique("King's Lynn And West Norfolk", 'Norfolk', 'UK')
            ->shouldBeCalledOnce()
            ->willReturn($district);

        $output = $this->districtService->findOrCreate("King's Lynn And West Norfolk", 'Norfolk', 'UK');

        $this->assertEntityManagerUnused();

        $this->assertEquals($district->reveal(), $output);
    }

    /**
     * @covers \App\Service\DistrictService::getLocaleSlugByDistrictSlug
     */
    public function testGetLocaleSlugByDistrictSlug1(): void
    {
        $district = $this->prophesize(District::class);
        $districtLocale = $this->prophesize(DistrictLocale::class);

        $this->districtRepository->findOneBySlug('test-district-slug')->willReturn($district->reveal());
        $this->localeService->findOrCreateByDistrict($district)->willReturn($districtLocale->reveal());
        $districtLocale->getSlug()->shouldBeCalledOnce()->willReturn('test-locale-slug');

        $output = $this->districtService->getLocaleSlugByDistrictSlug('test-district-slug');

        $this->assertEquals('test-locale-slug', $output);
    }
}
