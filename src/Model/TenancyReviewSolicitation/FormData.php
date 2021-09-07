<?php

namespace App\Model\TenancyReviewSolicitation;

use App\Model\Agency\Flat as FlatAgency;
use App\Model\Branch\Flat as FlatBranch;

class FormData
{
    /**
     * @param FlatBranch[] $branches
     */
    public function __construct(
        private FlatAgency $agency,
        private array $branches,
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
}
