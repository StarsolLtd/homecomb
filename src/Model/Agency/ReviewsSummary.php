<?php

namespace App\Model\Agency;

class ReviewsSummary
{
    public function __construct(
        private string $agencySlug,
        private string $agencyName,
        private ?string $agencyLogoImageFilename,
        private int $fiveStarCount,
        private int $fourStarCount,
        private int $threeStarCount,
        private int $twoStarCount,
        private int $oneStarCount,
        private int $ratedCount,
        private int $unratedCount,
        private float $meanRating,
    ) {
    }

    public function getAgencySlug(): string
    {
        return $this->agencySlug;
    }

    public function getAgencyName(): string
    {
        return $this->agencyName;
    }

    public function getAgencyLogoImageFilename(): ?string
    {
        return $this->agencyLogoImageFilename;
    }

    public function getRatedCount(): int
    {
        return $this->ratedCount;
    }

    public function getFiveStarCount(): int
    {
        return $this->fiveStarCount;
    }

    public function getFourStarCount(): int
    {
        return $this->fourStarCount;
    }

    public function getThreeStarCount(): int
    {
        return $this->threeStarCount;
    }

    public function getTwoStarCount(): int
    {
        return $this->twoStarCount;
    }

    public function getOneStarCount(): int
    {
        return $this->oneStarCount;
    }

    public function getUnratedCount(): int
    {
        return $this->unratedCount;
    }

    public function getMeanRating(): float
    {
        return $this->meanRating;
    }
}
