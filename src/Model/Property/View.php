<?php

namespace App\Model\Property;

use App\Model\City\Flat as FlatCity;
use App\Model\TenancyReview\View as ReviewView;

class View
{
    private string $slug;
    private ?string $addressLine1;
    private ?string $locality;
    private ?string $city;
    private ?string $postcode;
    private array $tenancyReviews;
    private ?float $latitude;
    private ?float $longitude;
    private ?FlatCity $city;

    public function __construct(
        string $slug,
        ?string $addressLine1,
        ?string $locality,
        ?string $city,
        ?string $postcode,
        array $tenancyReviews,
        ?float $latitude = null,
        ?float $longitude = null,
        ?FlatCity $city = null
    ) {
        $this->slug = $slug;
        $this->addressLine1 = $addressLine1;
        $this->locality = $locality;
        $this->city = $city;
        $this->postcode = $postcode;
        $this->tenancyReviews = $tenancyReviews;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->city = $city;
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

    public function getCity(): ?string
    {
        return $this->city;
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
}
