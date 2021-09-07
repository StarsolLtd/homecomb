<?php

namespace App\Model\TenancyReviewSolicitation;

use App\Model\Agency\Flat as FlatAgency;
use App\Model\Branch\Flat as FlatBranch;
use App\Model\Property\Flat as FlatProperty;

class View
{
    public function __construct(
        private string $code,
        private FlatAgency $agency,
        private FlatBranch $branch,
        private FlatProperty $property,
        private ?string $reviewerTitle,
        private string $reviewerFirstName,
        private string $reviewerLastName,
        private string $reviewerEmail,
    ) {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getAgency(): FlatAgency
    {
        return $this->agency;
    }

    public function getBranch(): FlatBranch
    {
        return $this->branch;
    }

    public function getProperty(): FlatProperty
    {
        return $this->property;
    }

    public function getReviewerTitle(): ?string
    {
        return $this->reviewerTitle;
    }

    public function getReviewerFirstName(): string
    {
        return $this->reviewerFirstName;
    }

    public function getReviewerLastName(): string
    {
        return $this->reviewerLastName;
    }

    public function getReviewerEmail(): string
    {
        return $this->reviewerEmail;
    }
}
