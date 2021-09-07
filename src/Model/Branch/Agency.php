<?php

namespace App\Model\Branch;

class Agency
{
    public function __construct(
        private string $slug,
        private string $name,
        private ?string $logoImageFilename,
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

    public function getLogoImageFilename(): ?string
    {
        return $this->logoImageFilename;
    }
}
