<?php

namespace App\Service\TenancyReviewSolicitation;

use App\Entity\TenancyReview;
use App\Exception\NotFoundException;
use App\Repository\TenancyReviewSolicitationRepositoryInterface;
use Psr\Log\LoggerInterface;

class CompleteService
{
    public function __construct(
        private TenancyReviewSolicitationRepositoryInterface $tenancyReviewSolicitationRepository,
        private LoggerInterface $logger
    ) {
    }

    public function complete(string $code, TenancyReview $tenancyReview): void
    {
        try {
            $rs = $this->tenancyReviewSolicitationRepository->findOneUnfinishedByCode($code);
            $rs->setTenancyReview($tenancyReview);
        } catch (NotFoundException $e) {
            $this->logger->error('Exception thrown completing TenancyReviewSolicitation: '.$e->getMessage());
        }
    }
}
