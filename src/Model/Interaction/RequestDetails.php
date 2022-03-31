<?php

namespace App\Model\Interaction;

class RequestDetails implements RequestDetailsInterface
{
    public function __construct(
        private ?string $sessionId = null,
        private ?string $ipAddress = null,
        private ?string $userAgent = null,
    ) {
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }
}
