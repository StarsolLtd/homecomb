<?php

namespace App\Model\Property;

class PropertySuggestion
{
    public function __construct(
        private string $address,
        private ?string $vendorId = null,
        private ?string $propertySlug = null,
    ) {
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
