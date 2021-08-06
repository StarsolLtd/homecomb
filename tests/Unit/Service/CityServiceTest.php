<?php

namespace App\Tests\Unit\Service;

use App\Entity\City;
use App\Factory\CityFactory;
use App\Repository\CityRepository;
use App\Service\CityService;
use App\Tests\Unit\EntityManagerTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Service\CityService
 */
class CityServiceTest extends TestCase
{
    use EntityManagerTrait;
    use ProphecyTrait;

    private CityService $cityService;

    private $entityManager;
    private $cityFactory;
    private $cityRepository;

    public function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->cityFactory = $this->prophesize(CityFactory::class);
        $this->cityRepository = $this->prophesize(CityRepository::class);

        $this->cityService = new CityService(
            $this->entityManager->reveal(),
            $this->cityFactory->reveal(),
            $this->cityRepository->reveal(),
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
}
