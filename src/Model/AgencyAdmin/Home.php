<?php

namespace App\Model\AgencyAdmin;

use App\Model\Agency\Flat as FlatAgency;
use App\Model\Branch\Flat as FlatBranch;
use App\Model\TenancyReview\View as TenancyReviewView;

class Home
{
    public function __construct(
        private FlatAgency $agency,
        /* @var FlatBranch[] */
        private array $branches = [],
        /* @var TenancyReviewView[] */
        private array $tenancyReviews = []
    ) {
    }

    public function getAgency(): FlatAgency
    {
        return $this->agency;
    }

    /**
     * @return FlatBranch[]
     */
    public function getBranches(): array
    {
        return $this->branches;
    }

    /**
     * @return TenancyReviewView[]
     */
    public function getTenancyReviews(): array
    {
        return $this->tenancyReviews;
    }
}
