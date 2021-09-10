<?php

namespace App\Model\Branch;

class Branch
{
    public function __construct(
        private string $slug,
        private string $name,
        private ?string $telephone,
        private ?string $email
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

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
}
