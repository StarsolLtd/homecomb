<?php

namespace App\Tests\Unit\Service\Agency;

use App\Entity\Agency;
use App\Factory\AgencyFactory;
use App\Repository\AgencyRepositoryInterface;
use App\Service\Agency\FindOrCreateService;
use App\Tests\Unit\EntityManagerTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class FindOrCreateServiceTest extends TestCase
{
    use ProphecyTrait;
    use EntityManagerTrait;

    private FindOrCreateService $findOrCreateService;

    private ObjectProphecy $agencyFactory;
    private ObjectProphecy $agencyRepository;

    public function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->agencyFactory = $this->prophesize(AgencyFactory::class);
        $this->agencyRepository = $this->prophesize(AgencyRepositoryInterface::class);

        $this->findOrCreateService = new FindOrCreateService(
            $this->entityManager->reveal(),
            $this->agencyFactory->reveal(),
            $this->agencyRepository->reveal(),
        );
    }

    public function testFindOrCreateByNameWhenNotExists(): void
    {
        $agencyName = 'Devon Homes';
        $agency = $this->prophesize(Agency::class);

        $this->agencyRepository->findOneBy(['name' => $agencyName])->shouldBeCalledOnce()->willReturn(null);

        $this->agencyFactory->createEntityByName($agencyName)->shouldBeCalledOnce()->willReturn($agency);

        $this->assertEntitiesArePersistedAndFlush([$agency]);

        $result = $this->findOrCreateService->findOrCreateByName($agencyName);

        $this->assertEquals($agency->reveal(), $result);
    }

    public function testFindOrCreateByNameWhenAlreadyExists(): void
    {
        $agencyName = 'Cornwall Homes';

        $agency = (new Agency())->setName($agencyName);

        $this->agencyRepository->findOneBy(['name' => $agencyName])->shouldBeCalledOnce()->willReturn($agency);

        $this->assertEntityManagerUnused();

        $result = $this->findOrCreateService->findOrCreateByName($agencyName);

        $this->assertEquals($agencyName, $result->getName());
    }
}
