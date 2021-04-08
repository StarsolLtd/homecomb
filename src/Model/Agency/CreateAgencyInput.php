<?php

namespace App\Model\Agency;

class CreateAgencyInput
{
    private string $agencyName;
    private ?string $externalUrl;
    private ?string $postcode;
    private ?string $captchaToken;

    public function __construct(
        string $agencyName,
        ?string $externalUrl = null,
        ?string $postcode = null,
        ?string $captchaToken = null
    ) {
        $this->agencyName = $agencyName;
        $this->externalUrl = $externalUrl;
        $this->postcode = $postcode;
        $this->captchaToken = $captchaToken;
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
