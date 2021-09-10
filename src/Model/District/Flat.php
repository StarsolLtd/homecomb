<?php

namespace App\Model\District;

class Flat
{
    public function __construct(
        private string $slug,
        private string $name,
        private ?string $county,
        private string $countryCode,
        private ?string $type,
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function isPublished(): bool
    {
        return $this->isPublished;
    }
}
