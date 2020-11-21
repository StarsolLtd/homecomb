<?php

namespace App\Model;

class LookupPropertyIdInput
{
    private string $addressLine1;
    private string $postcode;
    private string $countryCode;

    public function __construct(
        string $addressLine1,
        string $postcode,
        string $countryCode
    ) {
        $this->addressLine1 = $addressLine1;
        $this->postcode = $postcode;
        $this->countryCode = $countryCode;
    }

    public function getAddressLine1(): string
    {
        return $this->addressLine1;
    }

    public function getPostcode(): string
    {
        return $this->postcode;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }
}
