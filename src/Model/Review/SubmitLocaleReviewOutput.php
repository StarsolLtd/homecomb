<?php

namespace App\Model\Review;

class SubmitLocaleReviewOutput
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
