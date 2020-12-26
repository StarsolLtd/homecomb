<?php

namespace App\Model\Locale;

use App\Model\Review\View as ReviewView;

class View
{
    private string $slug;
    private string $name;
    private array $reviews;
    private ?AgencyReviewsSummary $agencyReviewsSummary;

    public function __construct(
        string $slug,
        string $name,
        array $reviews = [],
        ?AgencyReviewsSummary $agencyReviewsSummary = null
    ) {
        $this->slug = $slug;
        $this->name = $name;
        $this->reviews = $reviews;
        $this->agencyReviewsSummary = $agencyReviewsSummary;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return ReviewView[]
     */
    public function getReviews(): array
    {
        return $this->reviews;
    }

    public function getAgencyReviewsSummary(): ?AgencyReviewsSummary
    {
        return $this->agencyReviewsSummary;
    }
}
