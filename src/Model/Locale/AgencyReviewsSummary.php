<?php

namespace App\Model\Locale;

use App\Model\Agency\ReviewsSummary;

class AgencyReviewsSummary
{
    private array $agencyReviewSummaries;
    private int $tenancyReviewsCount;
    private int $agenciesCount;

    public function __construct(
        array $agencyReviewSummaries = [],
        int $tenancyReviewsCount = 0,
        int $agenciesCount = 0
    ) {
        $this->agencyReviewSummaries = $agencyReviewSummaries;
        $this->tenancyReviewsCount = $tenancyReviewsCount;
        $this->agenciesCount = $agenciesCount;
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
