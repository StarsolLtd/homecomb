<?php

namespace App\Model\Flag;

class SubmitInput
{
    private string $entityName;
    private int $entityId;
    private ?string $content;
    private ?string $googleReCaptchaToken;

    public function __construct(
        string $entityName,
        int $entityId,
        ?string $content = null,
        ?string $googleReCaptchaToken = null
    ) {
        $this->entityName = $entityName;
        $this->entityId = $entityId;
        $this->content = $content;
        $this->googleReCaptchaToken = $googleReCaptchaToken;
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

    public function getGoogleReCaptchaToken(): ?string
    {
        return $this->googleReCaptchaToken;
    }
}
