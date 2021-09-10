<?php

namespace App\Model\User;

class Flat
{
    public function __construct(
        private string $username,
        private ?string $title = null,
        private ?string $firstName = null,
        private ?string $lastName = null,
        private bool $agencyAdmin = false,
    ) {
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function isAgencyAdmin(): bool
    {
        return $this->agencyAdmin;
    }
}
