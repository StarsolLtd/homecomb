<?php

namespace App\Model\Agency;

class UpdateAgencyInput
{
    private ?string $externalUrl;
    private ?string $postcode;
    private ?string $captchaToken;

    public function __construct(
        ?string $externalUrl = null,
        ?string $postcode = null,
        ?string $captchaToken = null
    ) {
        $this->externalUrl = $externalUrl;
        $this->postcode = $postcode;
        $this->captchaToken = $captchaToken;
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
