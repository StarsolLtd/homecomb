<?php

namespace App\Model\Property;

class VendorProperty
{
    public function __construct(
        private ?string $vendorPropertyId,
        private string $addressLine1,
        private ?string $addressLine2,
        private ?string $addressLine3,
        private ?string $addressLine4,
        private ?string $locality,
        private string $city,
        private ?string $county,
        private ?string $district,
        private ?string $thoroughfare,
        private ?string $country,
        private string $postcode,
        private ?float $latitude,
        private ?float $longitude,
        private ?bool $residential = null,
    ) {
    }

    public function getVendorPropertyId(): ?string
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

    public function getThoroughFare(): ?string
    {
        return $this->thoroughfare;
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

    public function getResidential(): ?bool
    {
        return $this->residential;
    }
}
