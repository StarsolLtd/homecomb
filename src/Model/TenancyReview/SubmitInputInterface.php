<?php

namespace App\Model\TenancyReview;

interface SubmitInputInterface
{
    public function getPropertySlug(): string;

    public function getCode(): ?string;

    public function getReviewerName(): ?string;

    public function getStart(): ?string;

    public function getEnd(): ?string;

    public function getReviewerEmail(): ?string;

    public function getAgencyName(): ?string;

    public function getAgencyBranch(): ?string;

    public function getReviewTitle(): ?string;

    public function getReviewContent(): ?string;

    public function getOverallStars(): ?int;

    public function getAgencyStars(): ?int;

    public function getLandlordStars(): ?int;

    public function getPropertyStars(): ?int;
}
