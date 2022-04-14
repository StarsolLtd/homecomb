<?php

namespace App\Service\TenancyReviewSolicitation;

use App\Factory\TenancyReviewSolicitationFactory;
use App\Model\TenancyReviewSolicitation\View;
use App\Repository\TenancyReviewSolicitationRepositoryInterface;

class ViewService
{
    public function __construct(
        private TenancyReviewSolicitationFactory $tenancyReviewSolicitationFactory,
        private TenancyReviewSolicitationRepositoryInterface $tenancyReviewSolicitationRepository,
    ) {
    }

    public function getViewByCode(string $code): View
    {
        $rs = $this->tenancyReviewSolicitationRepository->findOneUnfinishedByCode($code);

        return $this->tenancyReviewSolicitationFactory->createViewByEntity($rs);
    }
}
