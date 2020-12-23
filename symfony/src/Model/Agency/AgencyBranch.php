<?php

namespace App\Model\Agency;

class AgencyBranch
{
    private string $slug;
    private string $name;
    private ?string $telephone;
    private ?string $email;

    public function __construct(
        string $slug,
        string $name,
        ?string $telephone,
        ?string $email
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
