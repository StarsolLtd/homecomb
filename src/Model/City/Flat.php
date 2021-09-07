<?php

namespace App\Model\City;

class Flat
{
    public function __construct(
        private string $slug,
        private string $name,
        private ?string $county,
        private string $countryCode,
        private bool $isPublished = false,
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

    public function isPublished(): bool
    {
        return $this->isPublished;
    }
}
