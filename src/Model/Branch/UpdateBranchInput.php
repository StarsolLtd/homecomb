<?php

namespace App\Model\Branch;

class UpdateBranchInput
{
    public function __construct(
        private ?string $telephone = null,
        private ?string $email = null,
        private ?string $captchaToken = null,
    ) {
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getCaptchaToken(): ?string
    {
        return $this->captchaToken;
    }
}
