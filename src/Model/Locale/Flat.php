<?php

namespace App\Model\Locale;

class Flat
{
    public function __construct(
        private string $slug,
        private string $name,
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

    public function isPublished(): bool
    {
        return $this->isPublished;
    }
}
