<?php

namespace App\Model\Property;

class PostcodeInput implements PostcodeInputInterface
{
    public function __construct(
        private string $postcode,
        private ?string $captchaToken = null,
    ) {
    }

    public function getPostcode(): string
    {
        return $this->postcode;
    }

    public function getCaptchaToken(): ?string
    {
        return $this->captchaToken;
    }
}
