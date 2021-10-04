<?php

namespace App\Tests\Unit\Service\AgencyA;

use App\Entity\Agency;
use App\Repository\AgencyRepository;
use App\Service\Agency\FindOrCreateService;
use App\Tests\Unit\EntityManagerTrait;
use App\Util\AgencyHelper;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class FindOrCreateServiceTest extends TestCase
{
    use ProphecyTrait;
    use EntityManagerTrait;

    private FindOrCreateService $findOrCreateService;

    private ObjectProphecy $agencyHelper;
    private ObjectProphecy $agencyRepository;

    public function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->agencyHelper = $this->prophesize(AgencyHelper::class);
        $this->agencyRepository = $this->prophesize(AgencyRepository::class);

        $this->findOrCreateService = new FindOrCreateService(
            $this->entityManager->reveal(),
            $this->agencyHelper->reveal(),
            $this->agencyRepository->reveal(),
        );
    }

    public function testFindOrCreateByNameWhenNotExists(): void
    {
        $agencyName = 'Devon Homes';

        $this->agencyRepository->findOneBy(['name' => $agencyName])->shouldBeCalledOnce()->willReturn(null);

        $this->agencyHelper->generateSlug(Argument::type(Agency::class))->shouldBeCalledOnce();

        $this->entityManager->persist(Argument::type(Agency::class))->shouldBeCalledOnce();
        $this->entityManager->flush()->shouldBeCalledTimes(1);

        $result = $this->findOrCreateService->findOrCreateByName($agencyName);

        $this->assertEquals($agencyName, $result->getName());
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
