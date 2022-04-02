<?php

namespace App\Model\Flag;

class SubmitInput implements SubmitInputInterface
{
    public function __construct(
        private string $entityName,
        private int $entityId,
        private ?string $content = null,
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getCaptchaToken(): ?string
    {
        return $this->captchaToken;
    }
}
