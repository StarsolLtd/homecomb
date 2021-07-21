<?php

namespace App\Model\Property;

class PropertySuggestion
{
    private string $address;
    private ?string $vendorId;
    private ?string $propertySlug;

    public function __construct(
        string $address,
        ?string $vendorId = null,
        ?string $propertySlug = null
    ) {
        $this->address = $address;
        $this->vendorId = $vendorId;
        $this->propertySlug = $propertySlug;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getVendorId(): ?string
    {
        return $this->vendorId;
    }

    public function getPropertySlug(): ?string
    {
        return $this->propertySlug;
    }
}
