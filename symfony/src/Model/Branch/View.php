<?php

namespace App\Model\Branch;

use App\Model\Review\View as ReviewView;

class View
{
    private Branch $branch;
    private ?Agency $agency;
    private array $reviews;

    public function __construct(
        Branch $branch,
        ?Agency $agency,
        array $reviews
    ) {
        $this->branch = $branch;
        $this->agency = $agency;
        $this->reviews = $reviews;
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
    public function getReviews(): array
    {
        return $this->reviews;
    }
}
