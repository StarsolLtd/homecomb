<?php

namespace App\Model\Interaction;

class RequestDetails
{
    private ?string $sessionId;
    private ?string $ipAddress;
    private ?string $userAgent;

    public function __construct(
        ?string $sessionId = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ) {
        $this->sessionId = $sessionId;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
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
