<?php

namespace App\Model\TenancyReview;

class SubmitOutput
{
    private bool $success;
    private int $entityId;

    public function __construct(
        bool $success,
        int $entityId
    ) {
        $this->success = $success;
        $this->entityId = $entityId;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getEntityId(): int
    {
        return $this->entityId;
    }
}
