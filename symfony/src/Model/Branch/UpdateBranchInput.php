<?php

namespace App\Model\Branch;

class UpdateBranchInput
{
    private ?string $telephone;
    private ?string $email;
    private ?string $captchaToken;

    public function __construct(
        ?string $telephone = null,
        ?string $email = null,
        ?string $captchaToken = null
    ) {
        $this->telephone = $telephone;
        $this->email = $email;
        $this->captchaToken = $captchaToken;
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
