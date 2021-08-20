<?php

namespace App\Model\Locale;

class Flat
{
    private string $slug;
    private string $name;
    private bool $isPublished;

    public function __construct(
        string $slug,
        string $name,
        bool $isPublished = false,
    ) {
        $this->slug = $slug;
        $this->name = $name;
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

    public function isPublished(): bool
    {
        return $this->isPublished;
    }
}
