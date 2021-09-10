<?php

namespace App\Model\Agency;

class Flat
{
    public function __construct(
        private string $slug,
        private string $name,
        private ?string $externalUrl = null,
        private ?string $postcode = null,
        private bool $isPublished = false,
        private ?string $logoImageFilename = null,
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

    public function getExternalUrl(): ?string
    {
        return $this->externalUrl;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    public function getLogoImageFilename(): ?string
    {
        return $this->logoImageFilename;
    }
}
