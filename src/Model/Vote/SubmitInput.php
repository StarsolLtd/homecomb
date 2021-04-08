<?php

namespace App\Model\Vote;

class SubmitInput
{
    private string $entityName;
    private int $entityId;
    private bool $positive;
    private ?string $captchaToken;

    public function __construct(
        string $entityName,
        int $entityId,
        bool $positive,
        ?string $captchaToken = null
    ) {
        $this->entityName = $entityName;
        $this->entityId = $entityId;
        $this->positive = $positive;
        $this->captchaToken = $captchaToken;
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
