<?php

namespace App\Model\Property;

class PostcodeProperties
{
    private string $postcode;
    private array $vendorProperties;

    /**
     * @param VendorProperty[] $vendorProperties
     */
    public function __construct(
        string $postcode,
        array $vendorProperties
    ) {
        $this->postcode = $postcode;
        $this->vendorProperties = $vendorProperties;
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
