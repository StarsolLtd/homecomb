<?php

namespace App\Tests\Unit\Service\TenancyReviewSolicitation;

use App\Entity\TenancyReviewSolicitation;
use App\Factory\TenancyReviewSolicitationFactory;
use App\Model\TenancyReviewSolicitation\View;
use App\Repository\TenancyReviewSolicitationRepository;
use App\Service\TenancyReviewSolicitation\GetViewService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class GetViewServiceTest extends TestCase
{
    use ProphecyTrait;

    private GetViewService $tenancyReviewSolicitationService;

    private ObjectProphecy $tenancyReviewSolicitationFactory;
    private ObjectProphecy $tenancyReviewSolicitationRepository;

    public function setUp(): void
    {
        $this->tenancyReviewSolicitationFactory = $this->prophesize(TenancyReviewSolicitationFactory::class);
        $this->tenancyReviewSolicitationRepository = $this->prophesize(TenancyReviewSolicitationRepository::class);

        $this->tenancyReviewSolicitationService = new GetViewService(
            $this->tenancyReviewSolicitationFactory->reveal(),
            $this->tenancyReviewSolicitationRepository->reveal(),
        );
    }

    public function testGetViewByCode1(): void
    {
        $rs = $this->prophesize(TenancyReviewSolicitation::class);
        $view = $this->prophesize(View::class);

        $this->tenancyReviewSolicitationRepository->findOneUnfinishedByCode('testcode')
            ->shouldBeCalledOnce()
            ->willReturn($rs);

        $this->tenancyReviewSolicitationFactory->createViewByEntity($rs)
            ->shouldBeCalledOnce()
            ->willReturn($view);

        $this->tenancyReviewSolicitationService->getViewByCode('testcode');
    }
}
