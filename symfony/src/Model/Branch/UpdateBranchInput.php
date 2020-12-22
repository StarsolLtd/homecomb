<?php

namespace App\Model\Branch;

class UpdateBranchInput
{
    private ?string $telephone;
    private ?string $email;
    private ?string $googleReCaptchaToken;

    public function __construct(
        ?string $telephone = null,
        ?string $email = null,
        ?string $googleReCaptchaToken = null
    ) {
        $this->telephone = $telephone;
        $this->email = $email;
        $this->googleReCaptchaToken = $googleReCaptchaToken;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getGoogleReCaptchaToken(): ?string
    {
        return $this->googleReCaptchaToken;
    }
}
