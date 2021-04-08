<?php

namespace App\Model\User;

class RegisterInput
{
    private string $email;
    private string $firstName;
    private string $lastName;
    private string $plainPassword;
    private ?string $captchaToken;

    public function __construct(
        string $email,
        string $firstName,
        string $lastName,
        string $plainPassword,
        ?string $captchaToken = null
    ) {
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->plainPassword = $plainPassword;
        $this->captchaToken = $captchaToken;
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
