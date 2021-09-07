<?php

namespace App\Model\Branch;

class Flat
{
    public function __construct(
        private string $slug,
        private string $name,
        private bool $isPublished = false,
        private ?string $telephone = null,
        private ?string $email = null,
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

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
}
