<?php

namespace App\Model\City;

use App\Model\Review\LocaleReviewView;

class City
{
    private string $slug;
    private string $name;
    private ?string $county;
    private string $countryCode;
    private array $localeReviews;

    public function __construct(
        string $slug,
        string $name,
        ?string $county,
        string $countryCode,
        array $localeReviews = [],
    ) {
        $this->slug = $slug;
        $this->name = $name;
        $this->county = $county;
        $this->countryCode = $countryCode;
        $this->localeReviews = $localeReviews;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCounty(): ?string
    {
        return $this->county;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @return LocaleReviewView[]
     */
    public function getLocaleReviews(): array
    {
        return $this->localeReviews;
    }
}
