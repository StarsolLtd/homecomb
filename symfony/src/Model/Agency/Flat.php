<?php

namespace App\Model\Agency;

class Flat
{
    private string $slug;
    private string $name;
    private ?string $logoImageFilename;

    public function __construct(
        string $slug,
        string $name,
        ?string $logoImageFilename = null
    ) {
        $this->slug = $slug;
        $this->name = $name;
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

    public function getLogoImageFilename(): ?string
    {
        return $this->logoImageFilename;
    }
}
