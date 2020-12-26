<?php

namespace App\Model\Branch;

class Flat
{
    private string $slug;
    private string $name;
    private ?string $telephone;
    private ?string $email;

    public function __construct(
        string $slug,
        string $name,
        ?string $telephone = null,
        ?string $email = null
    ) {
        $this->slug = $slug;
        $this->name = $name;
        $this->telephone = $telephone;
        $this->email = $email;
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
