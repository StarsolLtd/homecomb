<?php

namespace App\Model\Branch;

class CreateBranchInput
{
    private string $branchName;
    private ?string $telephone;
    private ?string $email;
    private ?string $googleReCaptchaToken;

    public function __construct(
        string $branchName,
        ?string $telephone = null,
        ?string $email = null,
        ?string $googleReCaptchaToken = null
    ) {
        $this->branchName = $branchName;
        $this->telephone = $telephone;
        $this->email = $email;
        $this->googleReCaptchaToken = $googleReCaptchaToken;
    }

    public function getBranchName(): string
    {
        return $this->branchName;
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
