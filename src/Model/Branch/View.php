<?php

namespace App\Model\Branch;

use App\Model\TenancyReview\View as ReviewView;

class View
{
    private Branch $branch;
    private ?Agency $agency;
    private array $tenancyReviews;

    public function __construct(
        Branch $branch,
        ?Agency $agency,
        array $tenancyReviews
    ) {
        $this->branch = $branch;
        $this->agency = $agency;
        $this->tenancyReviews = $tenancyReviews;
    }

    public function getBranch(): Branch
    {
        return $this->branch;
    }

    public function getAgency(): ?Agency
    {
        return $this->agency;
    }

    /**
     * @return ReviewView[]
     */
    public function getTenancyReviews(): array
    {
        return $this->tenancyReviews;
    }
}
