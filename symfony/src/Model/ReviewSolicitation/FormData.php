<?php

namespace App\Model\ReviewSolicitation;

use App\Model\Agency\Flat as FlatAgency;
use App\Model\Branch\Flat as FlatBranch;

class FormData
{
    private FlatAgency $agency;
    /** @var FlatBranch[] */
    private array $branches;

    public function __construct(
        FlatAgency $agency,
        array $branches
    ) {
        $this->agency = $agency;
        $this->branches = $branches;
    }

    public function getAgency(): FlatAgency
    {
        return $this->agency;
    }

    public function getBranches(): array
    {
        return $this->branches;
    }
}
