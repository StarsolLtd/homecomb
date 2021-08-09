<?php

namespace App\Model\District;

class Flat
{
    private string $slug;
    private string $name;
    private ?string $county;
    private string $countryCode;
    private ?string $type;
    private bool $isPublished;

    public function __construct(
        string $slug,
        string $name,
        ?string $county,
        string $countryCode,
        ?string $type,
        bool $isPublished = false,
    ) {
        $this->slug = $slug;
        $this->name = $name;
        $this->county = $county;
        $this->countryCode = $countryCode;
        $this->countryCode = $countryCode;
        $this->type = $type;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function isPublished(): bool
    {
        return $this->isPublished;
    }
}
