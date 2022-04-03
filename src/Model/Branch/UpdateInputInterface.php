<?php

namespace App\Model\Branch;

interface UpdateInputInterface
{
    public function getTelephone(): ?string;

    public function getEmail(): ?string;
}
