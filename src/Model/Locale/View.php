<?php

namespace App\Model\Locale;

use App\Model\Review\LocaleReviewView;
use App\Model\TenancyReview\View as ReviewView;

class View
{
    private string $slug;
    private string $name;
    private ?string $content;
    private array $localeReviews;
    private array $tenancyReviews;
    private ?AgencyReviewsSummary $agencyReviewsSummary;

    public function __construct(
        string $slug,
        string $name,
        ?string $content = null,
        array $localeReviews = [],
        array $tenancyReviews = [],
        ?AgencyReviewsSummary $agencyReviewsSummary = null
    ) {
        $this->slug = $slug;
        $this->name = $name;
        $this->content = $content;
        $this->localeReviews = $localeReviews;
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
     * @return LocaleReviewView[]
     */
    public function getLocaleReviews(): array
    {
        return $this->localeReviews;
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
