<?php

namespace App\Service\TenancyReviewSolicitation;

use App\Entity\TenancyReview;
use App\Exception\NotFoundException;
use App\Repository\TenancyReviewSolicitationRepository;
use Psr\Log\LoggerInterface;

class CompleteService
{
    public function __construct(
        private TenancyReviewSolicitationRepository $tenancyReviewSolicitationRepository,
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
