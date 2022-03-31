<?php

namespace App\Model\Agency;

class CreateAgencyInput implements CreateAgencyInputInterface
{
    public function __construct(
        private string $agencyName,
        private ?string $externalUrl = null,
        private ?string $postcode = null,
        private ?string $captchaToken = null
    ) {
    }

    public function getAgencyName(): string
    {
        return $this->agencyName;
    }

    public function getExternalUrl(): ?string
    {
        return $this->externalUrl;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function getCaptchaToken(): ?string
    {
        return $this->captchaToken;
    }
}
