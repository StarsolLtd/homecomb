<?php

namespace App\Model\BroadbandProviderReview;

class SubmitOutput
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
