<?php

namespace App\Tests\Unit\Service\Branch;

use App\Entity\Branch;
use App\Factory\BranchFactory;
use App\Model\Branch\View;
use App\Repository\BranchRepository;
use App\Service\Branch\BranchGetViewService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class BranchGetViewServiceTest extends TestCase
{
    use ProphecyTrait;

    private BranchGetViewService $branchGetViewService;

    private ObjectProphecy $branchFactory;
    private ObjectProphecy $branchRepository;

    public function setUp(): void
    {
        $this->branchFactory = $this->prophesize(BranchFactory::class);
        $this->branchRepository = $this->prophesize(BranchRepository::class);

        $this->branchGetViewService = new BranchGetViewService(
            $this->branchFactory->reveal(),
            $this->branchRepository->reveal(),
        );
    }

    public function testGetViewBySlug(): void
    {
        $branch = $this->prophesize(Branch::class);
        $view = $this->prophesize(View::class);

        $this->branchRepository->findOnePublishedBySlug('branchslug')
            ->shouldBeCalledOnce()
            ->willReturn($branch);

        $this->branchFactory->createViewFromEntity($branch)
            ->shouldBeCalledOnce()
            ->willReturn($view);

        $actual = $this->branchGetViewService->getViewBySlug('branchslug');

        $this->assertEquals($actual, $view->reveal());
    }
}
