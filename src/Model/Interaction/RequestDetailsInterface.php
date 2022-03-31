<?php

namespace App\Model\Interaction;

interface RequestDetailsInterface
{
    public function getSessionId(): ?string;

    public function getIpAddress(): ?string;

    public function getUserAgent(): ?string;
}
