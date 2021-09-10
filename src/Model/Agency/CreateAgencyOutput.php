<?php

namespace App\Model\Agency;

class CreateAgencyOutput
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
