<?php

namespace App\Model\Review;

interface SubmitLocaleReviewInputInterface
{
    public function getLocaleSlug(): string;

    public function getCode(): ?string;

    public function getReviewerName(): ?string;

    public function getReviewerEmail(): ?string;

    public function getReviewTitle(): ?string;

    public function getReviewContent(): ?string;

    public function getOverallStars(): ?int;
}
