<?php

namespace App\Model\Locale;

use App\Model\Agency\ReviewsSummary;

class AgencyReviewsSummary
{
    public function __construct(
        private array $agencyReviewSummaries = [],
        private int $tenancyReviewsCount = 0,
        private int $agenciesCount = 0,
    ) {
    }

    /**
     * @return ReviewsSummary[]
     */
    public function getAgencyReviewSummaries(): array
    {
        return $this->agencyReviewSummaries;
    }

    public function getTenancyReviewsCount(): int
    {
        return $this->tenancyReviewsCount;
    }

    public function getAgenciesCount(): int
    {
        return $this->agenciesCount;
    }
}
