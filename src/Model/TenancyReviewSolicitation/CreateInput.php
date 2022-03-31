<?php

namespace App\Model\TenancyReviewSolicitation;

class CreateInput implements CreateInputInterface
{
    public function __construct(
        private string $branchSlug,
        private string $propertySlug,
        private ?string $recipientTitle,
        private string $recipientFirstName,
        private string $recipientLastName,
        private string $recipientEmail,
        private ?string $captchaToken,
    ) {
    }

    public function getBranchSlug(): string
    {
        return $this->branchSlug;
    }

    public function getPropertySlug(): string
    {
        return $this->propertySlug;
    }

    public function getRecipientTitle(): ?string
    {
        return $this->recipientTitle;
    }

    public function getRecipientFirstName(): string
    {
        return $this->recipientFirstName;
    }

    public function getRecipientLastName(): string
    {
        return $this->recipientLastName;
    }

    public function getRecipientEmail(): string
    {
        return $this->recipientEmail;
    }

    public function getCaptchaToken(): ?string
    {
        return $this->captchaToken;
    }
}
