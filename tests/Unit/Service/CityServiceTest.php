<?php

namespace App\Tests\Unit\Service;

use App\Entity\City;
use App\Entity\Locale\CityLocale;
use App\Factory\CityFactory;
use App\Repository\CityRepository;
use App\Service\CityService;
use App\Service\Locale\FindOrCreateService as LocaleFindOrCreateService;
use App\Tests\Unit\EntityManagerTrait;
use Doctrine\ORM\EntityManagerInterface;
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

    private ObjectProphecy $cityFactory;
    private ObjectProphecy $cityRepository;
    private ObjectProphecy $localeFindOrCreateService;

    public function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->cityFactory = $this->prophesize(CityFactory::class);
        $this->cityRepository = $this->prophesize(CityRepository::class);
        $this->localeFindOrCreateService = $this->prophesize(LocaleFindOrCreateService::class);

        $this->cityService = new CityService(
            $this->entityManager->reveal(),
            $this->cityFactory->reveal(),
            $this->cityRepository->reveal(),
            $this->localeFindOrCreateService->reveal(),
        );
    }

    /**
     * @covers \App\Service\CityService::findOrCreate
     * Test an entity is created and persisted when it does not already exist.
     */
    public function testFindOrCreate1(): void
    {
        $city = $this->prophesize(City::class);

        $this->cityRepository->findOneByUnique('Lincoln', 'Lincolnshire', 'UK')
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $this->cityFactory->createEntity('Lincoln', 'Lincolnshire', 'UK')
            ->shouldBeCalledOnce()
            ->willReturn($city);

        $output = $this->cityService->findOrCreate('Lincoln', 'Lincolnshire', 'UK');

        $this->assertEntitiesArePersistedAndFlush([$city]);

        $this->assertEquals($city->reveal(), $output);
    }

    /**
     * @covers \App\Service\CityService::findOrCreate
     * Test that if the repository finds a record, that entity is returned.
     */
    public function testFindOrCreate2(): void
    {
        $city = $this->prophesize(City::class);

        $this->cityRepository->findOneByUnique('Lincoln', 'Lincolnshire', 'UK')
            ->shouldBeCalledOnce()
            ->willReturn($city);

        $output = $this->cityService->findOrCreate('Lincoln', 'Lincolnshire', 'UK');

        $this->assertEntityManagerUnused();

        $this->assertEquals($city->reveal(), $output);
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
