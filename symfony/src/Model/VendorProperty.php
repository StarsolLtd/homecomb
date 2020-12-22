<?php

namespace App\Model;

class VendorProperty
{
    private string $vendorPropertyId;
    private string $addressLine1;
    private ?string $addressLine2;
    private ?string $addressLine3;
    private ?string $addressLine4;
    private ?string $locality;
    private string $city;
    private ?string $county;
    private ?string $district;
    private ?string $country;
    private string $postcode;
    private ?float $latitude;
    private ?float $longitude;
    private bool $residential;

    public function __construct(
        string $vendorPropertyId,
        string $addressLine1,
        ?string $addressLine2,
        ?string $addressLine3,
        ?string $addressLine4,
        ?string $locality,
        string $city,
        ?string $county,
        ?string $district,
        ?string $country,
        string $postcode,
        ?float $latitude,
        ?float $longitude,
        bool $residential
    ) {
        $this->vendorPropertyId = $vendorPropertyId;
        $this->addressLine1 = $addressLine1;
        $this->addressLine2 = $addressLine2;
        $this->addressLine3 = $addressLine3;
        $this->addressLine4 = $addressLine4;
        $this->locality = $locality;
        $this->city = $city;
        $this->county = $county;
        $this->district = $district;
        $this->country = $country;
        $this->postcode = $postcode;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->residential = $residential;
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

    public function getAddressLine4(): ?string
    {
        return $this->addressLine4;
    }

    public function getLocality(): ?string
    {
        return $this->locality;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCounty(): ?string
    {
        return $this->county;
    }

    public function getDistrict(): ?string
    {
        return $this->district;
    }

    public function getCountry(): ?string
    {
        return $this->country;
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

    public function isResidential(): bool
    {
        return $this->residential;
    }
}
