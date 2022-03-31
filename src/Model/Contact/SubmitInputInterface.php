<?php

namespace App\Model\Contact;

interface SubmitInputInterface
{
    public function getEmailAddress(): string;

    public function getName(): string;

    public function getMessage(): string;
}
