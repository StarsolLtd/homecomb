<?php

namespace App\Model\Agency;

interface CreateAgencyInputInterface
{
    public function getAgencyName(): string;

    public function getExternalUrl(): ?string;

    public function getPostcode(): ?string;
}
