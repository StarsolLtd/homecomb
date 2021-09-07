<?php

namespace App\Model\TenancyReviewSolicitation;

class CreateReviewSolicitationOutput
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
