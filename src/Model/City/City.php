<?php

namespace App\Model\City;

use App\Model\Review\LocaleReviewView;

class City
{
    public function __construct(
        private string $slug,
        private string $name,
        private ?string $county,
        private string $countryCode,
        private array $localeReviews = [],
    ) {
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
