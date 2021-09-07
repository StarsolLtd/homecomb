<?php

namespace App\Model\Property;

class PostcodeProperties
{
    /**
     * @param VendorProperty[] $vendorProperties
     */
    public function __construct(
        private string $postcode,
        private array $vendorProperties,
    ) {
    }

    public function getPostcode(): string
    {
        return $this->postcode;
    }

    /**
     * @return VendorProperty[]
     */
    public function getVendorProperties(): array
    {
        return $this->vendorProperties;
    }
}
