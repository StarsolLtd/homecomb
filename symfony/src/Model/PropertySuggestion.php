<?php

namespace App\Model;

class PropertySuggestion
{
    private string $address;
    private string $vendorId;

    public function __construct(
        string $address,
        string $vendorId
    ) {
        $this->address = $address;
        $this->vendorId = $vendorId;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getVendorId(): string
    {
        return $this->vendorId;
    }
}
