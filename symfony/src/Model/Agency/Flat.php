<?php

namespace App\Model\Agency;

class Flat
{
    private string $slug;
    private string $name;
    private ?string $externalUrl;
    private ?string $postcode;
    private bool $isPublished;
    private ?string $logoImageFilename;

    public function __construct(
        string $slug,
        string $name,
        ?string $externalUrl = null,
        ?string $postcode = null,
        bool $isPublished = false,
        ?string $logoImageFilename = null
    ) {
        $this->slug = $slug;
        $this->name = $name;
        $this->externalUrl = $externalUrl;
        $this->postcode = $postcode;
        $this->isPublished = $isPublished;
        $this->logoImageFilename = $logoImageFilename;
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
