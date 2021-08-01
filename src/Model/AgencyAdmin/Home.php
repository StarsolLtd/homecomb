<?php

namespace App\Model\AgencyAdmin;

use App\Model\Agency\Flat as FlatAgency;
use App\Model\Branch\Flat as FlatBranch;
use App\Model\TenancyReview\View as TenancyReviewView;

class Home
{
    private FlatAgency $agency;
    /** @var FlatBranch[] */
    private array $branches;
    /** @var TenancyReviewView[] */
    private array $tenancyReviews;

    public function __construct(
        FlatAgency $agency,
        array $branches = [],
        array $tenancyReviews = []
    ) {
        $this->agency = $agency;
        $this->branches = $branches;
        $this->tenancyReviews = $tenancyReviews;
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
