<?php

namespace App\Model\Branch;

class Agency
{
    private string $slug;
    private string $name;
    private ?string $logoImageFilename;

    public function __construct(
        string $slug,
        string $name,
        ?string $logoImageFilename
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
