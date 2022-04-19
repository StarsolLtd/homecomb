<?php

namespace App\Model\Agency;

interface UpdateInputInterface
{
    public function getExternalUrl(): ?string;

    public function getPostcode(): ?string;
}
