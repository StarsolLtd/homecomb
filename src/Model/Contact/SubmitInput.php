<?php

namespace App\Model\Contact;

class SubmitInput
{
    private string $emailAddress;
    private string $name;
    private string $message;
    private ?string $captchaToken;

    public function __construct(
        string $emailAddress,
        string $name,
        string $message,
        ?string $captchaToken = null
    ) {
        $this->emailAddress = $emailAddress;
        $this->name = $name;
        $this->message = $message;
        $this->captchaToken = $captchaToken;
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
