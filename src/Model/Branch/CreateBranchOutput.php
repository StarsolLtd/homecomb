<?php

namespace App\Model\Branch;

class CreateBranchOutput
{
    private bool $success;

    public function __construct(
        bool $success
    ) {
        $this->success = $success;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }
}
