<?php

namespace App\Tests\Unit\Service\Branch;

use App\Entity\Branch;
use App\Factory\BranchFactory;
use App\Model\Branch\View;
use App\Repository\BranchRepositoryInterface;
use App\Service\Branch\ViewService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class ViewServiceTest extends TestCase
{
    use ProphecyTrait;

    private ViewService $branchGetViewService;

    private ObjectProphecy $branchFactory;
    private ObjectProphecy $branchRepository;

    public function setUp(): void
    {
        $this->branchFactory = $this->prophesize(BranchFactory::class);
        $this->branchRepository = $this->prophesize(BranchRepositoryInterface::class);

        $this->branchGetViewService = new ViewService(
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
