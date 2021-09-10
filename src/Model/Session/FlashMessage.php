<?php

namespace App\Model\Session;

class FlashMessage
{
    public function __construct(
        private string $type,
        private string $message,
    ) {
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
