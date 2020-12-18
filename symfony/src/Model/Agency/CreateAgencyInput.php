<?php

namespace App\Model\Agency;

class CreateAgencyInput
{
    private string $agencyName;
    private ?string $externalUrl;
    private ?string $postcode;
    private ?string $googleReCaptchaToken;

    public function __construct(
        string $agencyName,
        ?string $externalUrl = null,
        ?string $postcode = null,
        ?string $googleReCaptchaToken = null
    ) {
        $this->agencyName = $agencyName;
        $this->externalUrl = $externalUrl;
        $this->postcode = $postcode;
        $this->googleReCaptchaToken = $googleReCaptchaToken;
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

    public function getGoogleReCaptchaToken(): ?string
    {
        return $this->googleReCaptchaToken;
    }
}
