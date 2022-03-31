<?php

namespace App\Model\TenancyReviewSolicitation;

interface CreateInputInterface
{
    public function getBranchSlug(): string;

    public function getPropertySlug(): string;

    public function getRecipientTitle(): ?string;

    public function getRecipientFirstName(): string;

    public function getRecipientLastName(): string;

    public function getRecipientEmail(): string;

    public function getCaptchaToken(): ?string;
}
