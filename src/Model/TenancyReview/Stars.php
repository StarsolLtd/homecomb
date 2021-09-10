<?php

namespace App\Model\TenancyReview;

class Stars
{
    public function __construct(
        private ?int $overall,
        private ?int $property,
        private ?int $agency,
        private ?int $landlord
    ) {
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
