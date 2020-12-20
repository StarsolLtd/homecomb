<?php

namespace App\Model\ReviewSolicitation;

class CreateReviewSolicitationOutput
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
