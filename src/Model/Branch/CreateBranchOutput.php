<?php

namespace App\Model\Branch;

class CreateBranchOutput
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
