<?php

namespace App\Model\User;

class Flat
{
    private string $username;
    private ?string $title;
    private ?string $firstName;
    private ?string $lastName;

    public function __construct(
        string $username,
        ?string $title = null,
        ?string $firstName = null,
        ?string $lastName = null
    ) {
        $this->username = $username;
        $this->title = $title;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
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
}
