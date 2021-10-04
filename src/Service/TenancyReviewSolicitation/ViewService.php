<?php

namespace App\Service\TenancyReviewSolicitation;

use App\Factory\TenancyReviewSolicitationFactory;
use App\Model\TenancyReviewSolicitation\View;
use App\Repository\TenancyReviewSolicitationRepository;

class ViewService
{
    public function __construct(
        private TenancyReviewSolicitationFactory $tenancyReviewSolicitationFactory,
        private TenancyReviewSolicitationRepository $tenancyReviewSolicitationRepository,
    ) {
    }

    public function getViewByCode(string $code): View
    {
        $rs = $this->tenancyReviewSolicitationRepository->findOneUnfinishedByCode($code);

        return $this->tenancyReviewSolicitationFactory->createViewByEntity($rs);
    }
}
