<?php

namespace App\Service;

use App\Model\Contact\SubmitInput;
use App\Model\Contact\SubmitOutput;

class ContactService
{
    private EmailService $emailService;
    private string $siteName;
    private string $siteAdminEmail;

    public function __construct(
        EmailService $emailService,
        string $siteName,
        string $siteAdminEmail
    ) {
        $this->emailService = $emailService;
        $this->siteName = $siteName;
        $this->siteAdminEmail = $siteAdminEmail;
    }

    public function submitContact(SubmitInput $submitInput): SubmitOutput
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
