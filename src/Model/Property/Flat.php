<?php

namespace App\Model\Property;

class Flat
{
    public function __construct(
        private string $slug,
        private string $addressLine1,
        private string $postcode,
    ) {
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
