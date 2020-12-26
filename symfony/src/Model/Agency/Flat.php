<?php

namespace App\Model\Agency;

class Flat
{
    private string $slug;
    private string $name;
    private bool $isPublished;
    private ?string $logoImageFilename;

    public function __construct(
        string $slug,
        string $name,
        bool $isPublished = false,
        ?string $logoImageFilename = null
    ) {
        $this->slug = $slug;
        $this->name = $name;
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

    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    public function getLogoImageFilename(): ?string
    {
        return $this->logoImageFilename;
    }
}
