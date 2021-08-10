<?php

namespace App\Model\Property;

use App\Model\City\Flat as FlatCity;
use App\Model\District\Flat as FlatDistrict;
use App\Model\TenancyReview\View as ReviewView;

class View
{
    private string $slug;
    private ?string $addressLine1;
    private ?string $locality;
    private ?string $addressCity;
    private ?string $postcode;
    private array $tenancyReviews;
    private ?float $latitude;
    private ?float $longitude;
    private ?FlatCity $city;
    private ?FlatDistrict $district;

    public function __construct(
        string $slug,
        ?string $addressLine1,
        ?string $locality,
        ?string $addressCity,
        ?string $postcode,
        array $tenancyReviews,
        ?float $latitude = null,
        ?float $longitude = null,
        ?FlatCity $city = null,
        ?FlatDistrict $district = null
    ) {
        $this->slug = $slug;
        $this->addressLine1 = $addressLine1;
        $this->locality = $locality;
        $this->addressCity = $addressCity;
        $this->postcode = $postcode;
        $this->tenancyReviews = $tenancyReviews;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->city = $city;
        $this->district = $district;
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

    public function getCity(): ?FlatCity
    {
        return $this->city;
    }

    public function getDistrict(): ?FlatDistrict
    {
        return $this->district;
    }
}
