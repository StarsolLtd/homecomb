<?php

namespace App\Model;

class VendorProperty
{
    private string $vendorPropertyId;
    private string $addressLine1;
    private ?string $addressLine2;
    private ?string $addressLine3;
    private string $city;
    private string $postcode;
    private ?float $latitude;
    private ?float $longitude;

    public function __construct(
        string $vendorPropertyId,
        string $addressLine1,
        ?string $addressLine2,
        ?string $addressLine3,
        string $city,
        string $postcode,
        ?float $latitude,
        ?float $longitude
    ) {
        $this->vendorPropertyId = $vendorPropertyId;
        $this->addressLine1 = $addressLine1;
        $this->addressLine2 = $addressLine2;
        $this->addressLine3 = $addressLine3;
        $this->city = $city;
        $this->postcode = $postcode;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function getVendorPropertyId(): string
    {
        return $this->vendorPropertyId;
    }

    public function getAddressLine1(): string
    {
        return $this->addressLine1;
    }

    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }

    public function getAddressLine3(): ?string
    {
        return $this->addressLine3;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getPostcode(): string
    {
        return $this->postcode;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }
}
