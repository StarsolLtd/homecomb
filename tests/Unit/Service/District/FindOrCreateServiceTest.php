<?php

namespace App\Tests\Unit\Service\District;

use App\Entity\District;
use App\Factory\DistrictFactory;
use App\Repository\DistrictRepositoryInterface;
use App\Service\District\FindOrCreateService;
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

    private ObjectProphecy $districtFactory;
    private ObjectProphecy $districtRepository;

    public function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->districtFactory = $this->prophesize(DistrictFactory::class);
        $this->districtRepository = $this->prophesize(DistrictRepositoryInterface::class);

        $this->findOrCreateService = new FindOrCreateService(
            $this->entityManager->reveal(),
            $this->districtFactory->reveal(),
            $this->districtRepository->reveal(),
        );
    }

    /**
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

        $output = $this->findOrCreateService->findOrCreate("King's Lynn And West Norfolk", 'Norfolk', 'UK');

        $this->assertEntitiesArePersistedAndFlush([$district]);

        $this->assertEquals($district->reveal(), $output);
    }

    /**
     * Test that if the repository finds a record, that entity is returned.
     */
    public function testFindOrCreate2(): void
    {
        $district = $this->prophesize(District::class);

        $this->districtRepository->findOneByUnique("King's Lynn And West Norfolk", 'Norfolk', 'UK')
            ->shouldBeCalledOnce()
            ->willReturn($district);

        $output = $this->findOrCreateService->findOrCreate("King's Lynn And West Norfolk", 'Norfolk', 'UK');

        $this->assertEntityManagerUnused();

        $this->assertEquals($district->reveal(), $output);
    }
}
