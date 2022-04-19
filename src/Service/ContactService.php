<?php

namespace App\Service;

use App\Model\Contact\SubmitInputInterface;
use App\Model\Contact\SubmitOutput;

class ContactService
{
    public function __construct(
        private EmailService $emailService,
        private string $siteName,
        private string $siteAdminEmail,
    ) {
    }

    public function submitContact(SubmitInputInterface $submitInput): SubmitOutput
    {
        $this->emailService->process(
            $this->siteAdminEmail,
            $this->siteName,
            $this->siteName.' Contact Form Submission',
            'contact',
            [
                'fromEmail' => $submitInput->getEmailAddress(),
                'fromName' => $submitInput->getName(),
                'message' => $submitInput->getMessage(),
            ]
        );

        return new SubmitOutput(true);
    }
}
