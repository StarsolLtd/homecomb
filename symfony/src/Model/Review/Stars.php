<?php

namespace App\Model\Review;

class Stars
{
    private ?int $overall;
    private ?int $property;
    private ?int $agency;
    private ?int $landlord;

    public function __construct(
        ?int $overall,
        ?int $property,
        ?int $agency,
        ?int $landlord
    ) {
        $this->overall = $overall;
        $this->property = $property;
        $this->agency = $agency;
        $this->landlord = $landlord;
    }

    public function getOverall(): ?int
    {
        return $this->overall;
    }

    public function getProperty(): ?int
    {
        return $this->property;
    }

    public function getAgency(): ?int
    {
        return $this->agency;
    }

    public function getLandlord(): ?int
    {
        return $this->landlord;
    }
}
