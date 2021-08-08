<?php

namespace App\Model\TenancyReview;

class SubmitOutput
{
    private bool $success;
    private string $entitySlug;

    public function __construct(
        bool $success,
        string $entitySlug
    ) {
        $this->success = $success;
        $this->entitySlug = $entitySlug;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getEntitySlug(): string
    {
        return $this->entitySlug;
    }
}
