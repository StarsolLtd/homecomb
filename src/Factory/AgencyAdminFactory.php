<?php

namespace App\Factory;

use App\Entity\Agency;
use App\Model\AgencyAdmin\Home;

class AgencyAdminFactory
{
    public function __construct(
        private FlatModelFactory $flatModelFactory,
        private TenancyReviewFactory $tenancyReviewFactory,
    ) {
    }

    public function getHome(Agency $agencyEntity): Home
    {
        $agency = $this->flatModelFactory->getAgencyFlatModel($agencyEntity);

        $branches = [];
        foreach ($agencyEntity->getBranches() as $branchEntity) {
            $branches[] = $this->flatModelFactory->getBranchFlatModel($branchEntity);
        }

        $tenancyReviews = [];
        foreach ($agencyEntity->getPublishedTenancyReviews() as $tenancyReviewEntity) {
            $tenancyReviews[] = $this->tenancyReviewFactory->createViewFromEntity($tenancyReviewEntity);
        }

        return new Home(
            $agency,
            $branches,
            $tenancyReviews
        );
    }
}
