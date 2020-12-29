<?php

namespace App\Factory;

use App\Entity\Agency;
use App\Model\AgencyAdmin\Home;

class AgencyAdminFactory
{
    private FlatModelFactory $flatModelFactory;
    private ReviewFactory $reviewFactory;

    public function __construct(
        FlatModelFactory $flatModelFactory,
        ReviewFactory $reviewFactory
    ) {
        $this->flatModelFactory = $flatModelFactory;
        $this->reviewFactory = $reviewFactory;
    }

    public function getHome(Agency $agencyEntity): Home
    {
        $agency = $this->flatModelFactory->getAgencyFlatModel($agencyEntity);

        $branches = [];
        foreach ($agencyEntity->getBranches() as $branchEntity) {
            $branches[] = $this->flatModelFactory->getBranchFlatModel($branchEntity);
        }

        $reviews = [];
        foreach ($agencyEntity->getPublishedReviews() as $reviewEntity) {
            $reviews[] = $this->reviewFactory->createViewFromEntity($reviewEntity);
        }

        return new Home(
            $agency,
            $branches,
            $reviews
        );
    }
}
