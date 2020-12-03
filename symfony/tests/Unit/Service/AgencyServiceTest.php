<?php

namespace App\Tests\Unit\Util;

use App\Entity\Agency;
use App\Repository\AgencyRepository;
use App\Service\AgencyService;
use App\Util\AgencyHelper;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class AgencyServiceTest extends TestCase
{
    use ProphecyTrait;

    private AgencyService $agencyService;

    private $entityManagerMock;
    private $agencyHelperMock;
    private $agencyRepositoryMock;

    public function setUp(): void
    {
        $this->entityManagerMock = $this->prophesize(EntityManagerInterface::class);
        $this->agencyHelperMock = $this->prophesize(AgencyHelper::class);
        $this->agencyRepositoryMock = $this->prophesize(AgencyRepository::class);

        $this->agencyService = new AgencyService(
            $this->entityManagerMock->reveal(),
            $this->agencyHelperMock->reveal(),
            $this->agencyRepositoryMock->reveal()
        );
    }

    public function testFindOrCreateByNameWhenNotExists(): void
    {
        $agencyName = 'Devon Homes';

        $this->agencyRepositoryMock->findOneBy(['name' => $agencyName])->shouldBeCalledOnce()->willReturn(null);

        $this->agencyHelperMock->generateSlug(Argument::type(Agency::class))->shouldBeCalledOnce();

        $this->entityManagerMock->persist(Argument::type(Agency::class))->shouldBeCalledOnce();
        $this->entityManagerMock->flush()->shouldBeCalledTimes(1);

        $result = $this->agencyService->findOrCreateByName($agencyName);

        $this->assertEquals($agencyName, $result->getName());
    }

    public function testFindOrCreateByNameWhenAlreadyExists(): void
    {
        $agencyName = 'Cornwall Homes';

        $agency = (new Agency())->setName($agencyName);

        $this->agencyRepositoryMock->findOneBy(['name' => $agencyName])->shouldBeCalledOnce()->willReturn($agency);

        $this->entityManagerMock->flush()->shouldNotBeCalled();

        $result = $this->agencyService->findOrCreateByName($agencyName);

        $this->assertEquals($agencyName, $result->getName());
    }
}
