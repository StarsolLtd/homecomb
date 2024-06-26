<?php

namespace App\Service\Agency;

use App\Factory\AgencyFactory;
use App\Model\Agency\AgencyView;
use App\Repository\AgencyRepositoryInterface;

class ViewService
{
    public function __construct(
        private AgencyFactory $agencyFactory,
        private AgencyRepositoryInterface $agencyRepository,
    ) {
    }

    public function getViewBySlug(string $agencySlug): AgencyView
    {
        $agency = $this->agencyRepository->findOnePublishedBySlug($agencySlug);

        return $this->agencyFactory->createViewFromEntity($agency);
    }
}
