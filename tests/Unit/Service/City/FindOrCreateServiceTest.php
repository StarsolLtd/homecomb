<?php

namespace App\Tests\Unit\Service\City;

use App\Entity\City;
use App\Factory\CityFactory;
use App\Repository\CityRepositoryInterface;
use App\Service\City\FindOrCreateService;
use App\Tests\Unit\EntityManagerTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class FindOrCreateServiceTest extends TestCase
{
    use EntityManagerTrait;
    use ProphecyTrait;

    private FindOrCreateService $findOrCreateService;

    private ObjectProphecy $cityFactory;
    private ObjectProphecy $cityRepository;

    public function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->cityFactory = $this->prophesize(CityFactory::class);
        $this->cityRepository = $this->prophesize(CityRepositoryInterface::class);

        $this->findOrCreateService = new FindOrCreateService(
            $this->entityManager->reveal(),
            $this->cityFactory->reveal(),
            $this->cityRepository->reveal(),
        );
    }

    /**
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

        $output = $this->findOrCreateService->findOrCreate('Lincoln', 'Lincolnshire', 'UK');

        $this->assertEntitiesArePersistedAndFlush([$city]);

        $this->assertEquals($city->reveal(), $output);
    }

    /**
     * Test that if the repository finds a record, that entity is returned.
     */
    public function testFindOrCreate2(): void
    {
        $city = $this->prophesize(City::class);

        $this->cityRepository->findOneByUnique('Lincoln', 'Lincolnshire', 'UK')
            ->shouldBeCalledOnce()
            ->willReturn($city);

        $output = $this->findOrCreateService->findOrCreate('Lincoln', 'Lincolnshire', 'UK');

        $this->assertEntityManagerUnused();

        $this->assertEquals($city->reveal(), $output);
    }
}
