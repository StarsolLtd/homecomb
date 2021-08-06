<?php

namespace App\Model\City;

class Flat
{
    private string $slug;
    private string $name;
    private ?string $county;
    private string $countryCode;
    private bool $isPublished;

    public function __construct(
        string $slug,
        string $name,
        ?string $county,
        string $countryCode,
        bool $isPublished = false,
    ) {
        $this->slug = $slug;
        $this->name = $name;
        $this->county = $county;
        $this->countryCode = $countryCode;
        $this->isPublished = $isPublished;
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
