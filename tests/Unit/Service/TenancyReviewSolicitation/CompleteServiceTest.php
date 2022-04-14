<?php

namespace App\Tests\Unit\Service;

use App\Entity\TenancyReview;
use App\Entity\TenancyReviewSolicitation;
use App\Exception\NotFoundException;
use App\Repository\TenancyReviewSolicitationRepositoryInterface;
use App\Service\TenancyReviewSolicitation\CompleteService;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;

final class CompleteServiceTest extends TestCase
{
    use ProphecyTrait;

    private CompleteService $tenancyReviewSolicitationService;

    private ObjectProphecy $tenancyReviewSolicitationRepository;
    private ObjectProphecy $logger;

    public function setUp(): void
    {
        $this->tenancyReviewSolicitationRepository = $this->prophesize(TenancyReviewSolicitationRepositoryInterface::class);
        $this->logger = $this->prophesize(LoggerInterface::class);

        $this->tenancyReviewSolicitationService = new CompleteService(
            $this->tenancyReviewSolicitationRepository->reveal(),
            $this->logger->reveal(),
        );
    }

    public function testComplete1(): void
    {
        $rs = $this->prophesize(TenancyReviewSolicitation::class);
        $tenancyReview = $this->prophesize(TenancyReview::class);

        $this->tenancyReviewSolicitationRepository->findOneUnfinishedByCode('testcode')
            ->shouldBeCalledOnce()
            ->willReturn($rs);

        $rs->setTenancyReview($tenancyReview)->shouldBeCalledOnce();

        $this->tenancyReviewSolicitationService->complete('testcode', $tenancyReview->reveal());
    }

    /**
     * Test logs error when not found.
     */
    public function testComplete2(): void
    {
        $tenancyReview = $this->prophesize(TenancyReview::class);

        $this->tenancyReviewSolicitationRepository->findOneUnfinishedByCode('testcode')
            ->shouldBeCalledOnce()
            ->willThrow(NotFoundException::class);

        $this->logger->error(Argument::type('string'))
            ->shouldBeCalledOnce();

        $this->tenancyReviewSolicitationService->complete('testcode', $tenancyReview->reveal());
    }
}
