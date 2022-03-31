<?php

namespace App\Model\User;

class RegisterInput implements RegisterInputInterface
{
    public function __construct(
        private string $email,
        private string $firstName,
        private string $lastName,
        private string $plainPassword,
        private ?string $captchaToken = null
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    public function getCaptchaToken(): ?string
    {
        return $this->captchaToken;
    }
}
