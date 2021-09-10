<?php

namespace App\Model\Branch;

class UpdateBranchOutput
{
    public function __construct(
        private bool $success,
    ) {
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }
}
