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
     * @covers \App\Service\BranchFindOrCreateService::findOrCreate
     */
    public function testFindOrCreateWithoutAgencyWhereBranchAlreadyExists(): void
    {
        $branch = new Branch();

        $this->branchRepository->findOneByNameWithoutAgencyOrNull('Test Name', null)
            ->shouldBeCalledOnce()
            ->willReturn($branch);

        $output = $this->branchFindOrCreateService->findOrCreate('Test Name', null);

        $this->assertEquals($branch, $output);
        $this->assertEntityManagerUnused();
    }

    /**
     * @covers \App\Service\BranchFindOrCreateService::findOrCreate
     */
    public function testFindOrCreateWhereBranchAlreadyExists(): void
    {
        $branch = new Branch();
        $agency = new Agency();

        $this->branchRepository->findOneByNameAndAgencyOrNull('Test Name', $agency)
            ->shouldBeCalledOnce()
            ->willReturn($branch);

        $output = $this->branchFindOrCreateService->findOrCreate('Test Name', $agency);

        $this->assertEquals($branch, $output);
        $this->assertEntityManagerUnused();
    }

    /**
     * @covers \App\Service\BranchFindOrCreateService::findOrCreate
     */
    public function testFindOrCreateWhereBranchDoesNotExists(): void
    {
        $agency = new Agency();

        $this->branchRepository->findOneByNameAndAgencyOrNull('Test Name', $agency)
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $this->branchHelper->generateSlug(Argument::type(Branch::class))
            ->shouldBeCalledOnce()
            ->willReturn('testslug');

        $this->entityManager->persist(Argument::type(Branch::class))->shouldBeCalledOnce();
        $this->entityManager->flush()->shouldBeCalledOnce();

        $output = $this->branchFindOrCreateService->findOrCreate('Test Name', $agency);

        $this->assertEquals('Test Name', $output->getName());
        $this->assertEquals($agency, $output->getAgency());
    }
}
