<?php

namespace App\Model\Contact;

class SubmitInput
{
    public function __construct(
        private string $emailAddress,
        private string $name,
        private string $message,
        private ?string $captchaToken = null,
    ) {
    }

    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCaptchaToken(): ?string
    {
        return $this->captchaToken;
    }
}
