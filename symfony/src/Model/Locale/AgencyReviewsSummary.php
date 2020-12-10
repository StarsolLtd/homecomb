<?php

namespace App\Model\Locale;

use App\Model\Agency\ReviewsSummary;

class AgencyReviewsSummary
{
    private array $agencyReviewSummaries;
    private int $reviewsCount;
    private int $agenciesCount;

    public function __construct(
        array $agencyReviewSummaries = [],
        int $reviewsCount = 0,
        int $agenciesCount = 0
    ) {
        $this->agencyReviewSummaries = $agencyReviewSummaries;
        $this->reviewsCount = $reviewsCount;
        $this->agenciesCount = $agenciesCount;
    }

    /**
     * @return ReviewsSummary[]
     */
    public function getAgencyReviewSummaries(): array
    {
        return $this->agencyReviewSummaries;
    }

    public function getReviewsCount(): int
    {
        return $this->reviewsCount;
    }

    public function getAgenciesCount(): int
    {
        return $this->agenciesCount;
    }
}
