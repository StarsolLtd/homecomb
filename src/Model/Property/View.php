<?php

namespace App\Model\Property;

use App\Model\City\City;
use App\Model\District\Flat as FlatDistrict;
use App\Model\TenancyReview\View as ReviewView;

class View
{
    public function __construct(
        private string $slug,
        private ?string $addressLine1,
        private ?string $locality,
        private ?string $addressCity,
        private ?string $postcode,
        private array $tenancyReviews,
        private ?float $latitude = null,
        private ?float $longitude = null,
        private ?City $city = null,
        private ?FlatDistrict $district = null,
    ) {
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getAddressLine1(): ?string
    {
        return $this->addressLine1;
    }

    public function getLocality(): ?string
    {
        return $this->locality;
    }

    public function getAddressCity(): ?string
    {
        return $this->addressCity;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    /**
     * @return ReviewView[]
     */
    public function getTenancyReviews(): array
    {
        return $this->tenancyReviews;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function getDistrict(): ?FlatDistrict
    {
        return $this->district;
    }
}
