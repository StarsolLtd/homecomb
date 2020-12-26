<?php

namespace App\Model\Property;

class Flat
{
    private string $slug;
    private string $addressLine1;
    private string $postcode;

    public function __construct(
        string $slug,
        string $addressLine1,
        string $postcode
    ) {
        $this->slug = $slug;
        $this->addressLine1 = $addressLine1;
        $this->postcode = $postcode;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getAddressLine1(): string
    {
        return $this->addressLine1;
    }

    public function getPostcode(): string
    {
        return $this->postcode;
    }
}
