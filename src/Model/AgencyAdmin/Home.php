<?php

namespace App\Model\AgencyAdmin;

use App\Model\Agency\Flat as FlatAgency;
use App\Model\Branch\Flat as FlatBranch;
use App\Model\Review\View as ReviewView;

class Home
{
    private FlatAgency $agency;
    /** @var FlatBranch[] */
    private array $branches;
    /** @var ReviewView[] */
    private array $reviews;

    public function __construct(
        FlatAgency $agency,
        array $branches = [],
        array $reviews = []
    ) {
        $this->agency = $agency;
        $this->branches = $branches;
        $this->reviews = $reviews;
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
     * @return ReviewView[]
     */
    public function getReviews(): array
    {
        return $this->reviews;
    }
}
