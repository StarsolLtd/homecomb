<?php

namespace App\Model\Agency;

interface CreateInputInterface
{
    public function getAgencyName(): string;

    public function getExternalUrl(): ?string;

    public function getPostcode(): ?string;
}
