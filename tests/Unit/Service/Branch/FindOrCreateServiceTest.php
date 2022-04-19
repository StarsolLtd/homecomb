<?php

namespace App\Tests\Unit\Service\Branch;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Repository\BranchRepositoryInterface;
use App\Service\Branch\FindOrCreateService;
use App\Tests\Unit\EntityManagerTrait;
use App\Tests\Unit\UserEntityFromInterfaceTrait;
use App\Util\BranchHelper;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class FindOrCreateServiceTest extends TestCase
{
    use EntityManagerTrait;
    use ProphecyTrait;
    use UserEntityFromInterfaceTrait;

    private FindOrCreateService $branchFindOrCreateService;

    private ObjectProphecy $branchHelper;
    private ObjectProphecy $branchRepository;

    public function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManager::class);
        $this->branchHelper = $this->prophesize(BranchHelper::class);
        $this->branchRepository = $this->prophesize(BranchRepositoryInterface::class);

        $this->branchFindOrCreateService = new FindOrCreateService(
            $this->entityManager->reveal(),
            $this->branchHelper->reveal(),
            $this->branchRepository->reveal(),
        );
    }

    /**
     * Test findOrCreate, without agency, where branch already exists.
     */
    public function testFindOrCreate1(): void
    {
        $branch = $this->prophesize(Branch::class);

        $this->branchRepository->findOneByNameWithoutAgencyOrNull('Test Name', null)
            ->shouldBeCalledOnce()
            ->willReturn($branch->reveal());

        $output = $this->branchFindOrCreateService->findOrCreate('Test Name', null);

        $this->assertEquals($branch->reveal(), $output);
        $this->assertEntityManagerUnused();
    }

    /**
     * Test findOrCreate, with agency, where branch already exists.
     */
    public function testFindOrCreate2(): void
    {
        $branch = $this->prophesize(Branch::class);
        $agency = $this->prophesize(Agency::class);

        $this->branchRepository->findOneByNameAndAgencyOrNull('Test Name', $agency)
            ->shouldBeCalledOnce()
            ->willReturn($branch->reveal());

        $output = $this->branchFindOrCreateService->findOrCreate('Test Name', $agency->reveal());

        $this->assertEquals($branch->reveal(), $output);
        $this->assertEntityManagerUnused();
    }

    /**
     * Test findOrCreate where branch does not exist.
     */
    public function testFindOrCreateWhereBranchDoesNotExists(): void
    {
        $agency = $this->prophesize(Agency::class);

        $this->branchRepository->findOneByNameAndAgencyOrNull('Test Name', $agency)
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $this->branchHelper->generateSlug(Argument::type(Branch::class))
            ->shouldBeCalledOnce()
            ->willReturn('testslug');

        $this->entityManager->persist(Argument::type(Branch::class))->shouldBeCalledOnce();
        $this->entityManager->flush()->shouldBeCalledOnce();

        $output = $this->branchFindOrCreateService->findOrCreate('Test Name', $agency->reveal());

        $this->assertEquals('Test Name', $output->getName());
        $this->assertEquals($agency->reveal(), $output->getAgency());
    }
}
