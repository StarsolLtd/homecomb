<?php

namespace App\Model\Branch;

class CreateBranchInput
{
    public function __construct(
        private string $branchName,
        private ?string $telephone = null,
        private ?string $email = null,
        private ?string $captchaToken = null,
    ) {
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

    public function getCaptchaToken(): ?string
    {
        return $this->captchaToken;
    }
}
