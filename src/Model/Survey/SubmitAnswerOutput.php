<?php

namespace App\Model\Survey;

class SubmitAnswerOutput
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
