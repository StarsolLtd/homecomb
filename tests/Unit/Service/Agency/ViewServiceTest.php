<?php

namespace App\Tests\Unit\Service\Agency;

use App\Entity\Agency;
use App\Factory\AgencyFactory;
use App\Model\Agency\AgencyView;
use App\Repository\AgencyRepository;
use App\Service\Agency\ViewService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class ViewServiceTest extends TestCase
{
    use ProphecyTrait;

    private ViewService $getViewService;

    private ObjectProphecy $agencyFactory;
    private ObjectProphecy $agencyRepository;

    public function setUp(): void
    {
        $this->agencyFactory = $this->prophesize(AgencyFactory::class);
        $this->agencyRepository = $this->prophesize(AgencyRepository::class);

        $this->getViewService = new ViewService(
            $this->agencyFactory->reveal(),
            $this->agencyRepository->reveal(),
        );
    }

    public function testGetViewBySlug1(): void
    {
        $agency = new Agency();
        $agencyView = $this->prophesize(AgencyView::class);

        $this->agencyRepository->findOnePublishedBySlug('agencyslug')
            ->shouldBeCalledOnce()
            ->willReturn($agency);

        $this->agencyFactory->createViewFromEntity($agency)
            ->shouldBeCalled()
            ->willReturn($agencyView);

        $output = $this->getViewService->getViewBySlug('agencyslug');

        $this->assertEquals($agencyView->reveal(), $output);
    }
}
