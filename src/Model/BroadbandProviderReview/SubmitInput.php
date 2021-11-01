<?php

namespace App\Model\BroadbandProviderReview;

class SubmitInput
{
    public function __construct(
        private ?string $broadbandProviderName = null,
        private ?string $broadbandProviderSlug = null,
        private ?string $reviewerName = null,
        private ?string $reviewerEmail = null,
        private ?string $reviewTitle = null,
        private ?string $reviewContent = null,
        private ?int $overallStars = null,
        private ?string $captchaToken = null,
    ) {
    }

    public function getBroadbandProviderName(): ?string
    {
        return $this->broadbandProviderName;
    }

    public function getBroadbandProviderSlug(): ?string
    {
        return $this->broadbandProviderSlug;
    }

    public function getReviewerName(): ?string
    {
        return $this->reviewerName;
    }

    public function getReviewerEmail(): ?string
    {
        return $this->reviewerEmail;
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

    public function getCaptchaToken(): ?string
    {
        return $this->captchaToken;
    }
}
