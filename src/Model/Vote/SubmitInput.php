<?php

namespace App\Model\Vote;

class SubmitInput
{
    public function __construct(
        private string $entityName,
        private int $entityId,
        private bool $positive,
        private ?string $captchaToken = null,
    ) {
    }

    public function getEntityName(): string
    {
        return $this->entityName;
    }

    public function getEntityId(): int
    {
        return $this->entityId;
    }

    public function isPositive(): bool
    {
        return $this->positive;
    }

    public function getCaptchaToken(): ?string
    {
        return $this->captchaToken;
    }
}
