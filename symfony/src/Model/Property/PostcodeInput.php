<?php

namespace App\Model\Property;

class PostcodeInput
{
    private string $postcode;
    private ?string $captchaToken;

    public function __construct(
        string $postcode,
        ?string $captchaToken = null
    ) {
        $this->postcode = $postcode;
        $this->captchaToken = $captchaToken;
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
