<?php

namespace App\Model\Flag;

class SubmitInput
{
    private string $entityName;
    private int $entityId;
    private ?string $content;
    private ?string $captchaToken;

    public function __construct(
        string $entityName,
        int $entityId,
        ?string $content = null,
        ?string $captchaToken = null
    ) {
        $this->entityName = $entityName;
        $this->entityId = $entityId;
        $this->content = $content;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getCaptchaToken(): ?string
    {
        return $this->captchaToken;
    }
}
