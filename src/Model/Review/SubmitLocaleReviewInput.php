<?php

namespace App\Model\Review;

class SubmitLocaleReviewInput
{
    private string $localeSlug;
    private ?string $tenancyReviewSlug;
    private ?string $reviewerName;
    private ?string $reviewerEmail;
    private ?string $reviewTitle;
    private ?string $reviewContent;
    private ?int $overallStars;
    private ?string $captchaToken;

    public function __construct(
        string $localeSlug,
        ?string $tenancyReviewSlug = null,
        ?string $reviewerName = null,
        ?string $reviewerEmail = null,
        ?string $reviewTitle = null,
        ?string $reviewContent = null,
        ?int $overallStars = null,
        ?string $captchaToken = null
    ) {
        $this->localeSlug = $localeSlug;
        $this->tenancyReviewSlug = $tenancyReviewSlug;
        $this->reviewerName = $reviewerName;
        $this->reviewerEmail = $reviewerEmail;
        $this->reviewTitle = $reviewTitle;
        $this->reviewContent = $reviewContent;
        $this->overallStars = $overallStars;
        $this->captchaToken = $captchaToken;
    }

    public function getLocaleSlug(): string
    {
        return $this->localeSlug;
    }

    public function getTenancyReviewSlug(): ?string
    {
        return $this->tenancyReviewSlug;
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
