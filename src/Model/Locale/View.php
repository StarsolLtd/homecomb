<?php

namespace App\Model\Locale;

use App\Model\TenancyReview\View as ReviewView;

class View
{
    private string $slug;
    private string $name;
    private ?string $content;
    private array $tenancyReviews;
    private ?AgencyReviewsSummary $agencyReviewsSummary;

    public function __construct(
        string $slug,
        string $name,
        ?string $content = null,
        array $tenancyReviews = [],
        ?AgencyReviewsSummary $agencyReviewsSummary = null
    ) {
        $this->slug = $slug;
        $this->name = $name;
        $this->content = $content;
        $this->tenancyReviews = $tenancyReviews;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @return ReviewView[]
     */
    public function getTenancyReviews(): array
    {
        return $this->tenancyReviews;
    }

    public function getAgencyReviewsSummary(): ?AgencyReviewsSummary
    {
        return $this->agencyReviewsSummary;
    }
}
