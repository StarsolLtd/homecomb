<?php

namespace App\Model\Branch;

interface CreateInputInterface
{
    public function getBranchName(): string;

    public function getTelephone(): ?string;

    public function getEmail(): ?string;
}
