<?php

namespace App\Model;

class SubmitReviewInput
{
    private int $propertyId;
    private ?string $reviewerName;
    private ?string $reviewerEmail;
    private ?string $agencyName;
    private ?string $agencyBranch;
    private ?string $reviewTitle;
    private ?string $reviewContent;
    private ?int $overallStars;
    private ?int $agencyStars;
    private ?int $landlordStars;
    private ?int $propertyStars;
    private ?string $googleReCaptchaToken;

    public function __construct(
        int $propertyId,
        ?string $reviewerName = null,
        ?string $reviewerEmail = null,
        ?string $agencyName = null,
        ?string $agencyBranch = null,
        ?string $reviewTitle = null,
        ?string $reviewContent = null,
        ?int $overallStars = null,
        ?int $agencyStars = null,
        ?int $landlordStars = null,
        ?int $propertyStars = null,
        ?string $googleReCaptchaToken = null
    ) {
        $this->propertyId = $propertyId;
        $this->reviewerName = $reviewerName;
        $this->reviewerEmail = $reviewerEmail;
        $this->agencyName = $agencyName;
        $this->agencyBranch = $agencyBranch;
        $this->reviewTitle = $reviewTitle;
        $this->reviewContent = $reviewContent;
        $this->overallStars = $overallStars;
        $this->agencyStars = $agencyStars;
        $this->landlordStars = $landlordStars;
        $this->propertyStars = $propertyStars;
        $this->googleReCaptchaToken = $googleReCaptchaToken;
    }

    public function getPropertyId(): int
    {
        return $this->propertyId;
    }

    public function getReviewerName(): ?string
    {
        return $this->reviewerName;
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

    public function getGoogleReCaptchaToken(): ?string
    {
        return $this->googleReCaptchaToken;
    }
}
