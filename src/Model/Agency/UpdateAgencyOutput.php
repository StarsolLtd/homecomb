<?php

namespace App\Model\Agency;

class UpdateAgencyOutput
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
