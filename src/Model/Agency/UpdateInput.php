<?php

namespace App\Model\Agency;

class UpdateInput implements UpdateInputInterface
{
    public function __construct(
        private ?string $externalUrl = null,
        private ?string $postcode = null,
        private ?string $captchaToken = null,
    ) {
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
