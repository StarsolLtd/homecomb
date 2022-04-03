<?php

namespace App\Model\Branch;

interface Branch
{
    public function getBranchName(): string;

    public function getTelephone(): ?string;

    public function getEmail(): ?string;
}
