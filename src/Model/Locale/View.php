<?php

namespace App\Model\Locale;

use App\Model\Review\View as ReviewView;

class View
{
    private string $slug;
    private string $name;
    private ?string $content;
    private array $reviews;
    private ?AgencyReviewsSummary $agencyReviewsSummary;

    public function __construct(
        string $slug,
        string $name,
        ?string $content = null,
        array $reviews = [],
        ?AgencyReviewsSummary $agencyReviewsSummary = null
    ) {
        $this->slug = $slug;
        $this->name = $name;
        $this->content = $content;
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

    public function getContent(): ?string
    {
        return $this->content;
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
