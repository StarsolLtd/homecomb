<?php

namespace App\Model;

class LookupPropertyIdInput
{
    private string $addressLine1;
    private string $postcode;
    private string $countryCode;
    private ?string $vendorPropertyId;

    public function __construct(
        string $addressLine1,
        string $postcode,
        string $countryCode,
        ?string $vendorPropertyId = null
    ) {
        $this->addressLine1 = $addressLine1;
        $this->postcode = $postcode;
        $this->countryCode = $countryCode;
        $this->vendorPropertyId = $vendorPropertyId;
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

    public function getVendorPropertyId(): ?string
    {
        return $this->vendorPropertyId;
    }
}
