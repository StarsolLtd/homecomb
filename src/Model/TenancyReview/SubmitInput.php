<?php

namespace App\Model\TenancyReview;

class SubmitInput
{
    public function __construct(
        private string $propertySlug,
        private ?string $code = null,
        private ?string $reviewerName = null,
        private ?string $reviewerEmail = null,
        private ?string $start = null,  // Month in YYYY-MM-01 format
        private ?string $end = null, // Month in YYYY-MM-01 format
        private ?string $agencyName = null,
        private ?string $agencyBranch = null,
        private ?string $reviewTitle = null,
        private ?string $reviewContent = null,
        private ?int $overallStars = null,
        private ?int $agencyStars = null,
        private ?int $landlordStars = null,
        private ?int $propertyStars = null,
        private ?string $captchaToken = null,
    ) {
    }

    public function getPropertySlug(): string
    {
        return $this->propertySlug;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getReviewerName(): ?string
    {
        return $this->reviewerName;
    }

    public function getStart(): ?string
    {
        return $this->start;
    }

    public function getEnd(): ?string
    {
        return $this->end;
    }

    public function getReviewerEmail(): ?string
    {
        return $this->reviewerEmail;
    }

    public function getAgencyName(): ?string
    {
        return $this->agencyName;
    }

    public function getAgencyBranch(): ?string
    {
        return $this->agencyBranch;
    }

    public function getReviewTitle(): ?string
    {
        return $this->reviewTitle;
    }

    public function getReviewContent(): ?string
    {
        return $this->reviewContent;
    }

    public function getOverallStars(): ?int
    {
        return $this->overallStars;
    }

    public function getAgencyStars(): ?int
    {
        return $this->agencyStars;
    }

    public function getLandlordStars(): ?int
    {
        return $this->landlordStars;
    }

    public function getPropertyStars(): ?int
    {
        return $this->propertyStars;
    }

    public function getCaptchaToken(): ?string
    {
        return $this->captchaToken;
    }
}
