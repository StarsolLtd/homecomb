<?php

namespace App\Model\Review;

class SubmitLocaleReviewInput
{
    public function __construct(
        private string $localeSlug,
        private ?string $code = null,
        private ?string $reviewerName = null,
        private ?string $reviewerEmail = null,
        private ?string $reviewTitle = null,
        private ?string $reviewContent = null,
        private ?int $overallStars = null,
        private ?string $captchaToken = null,
    ) {
    }

    public function getLocaleSlug(): string
    {
        return $this->localeSlug;
    }

    public function getCode(): ?string
    {
        return $this->code;
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
