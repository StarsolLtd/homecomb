<?php

namespace App\Service\TenancyReviewSolicitation;

use App\Entity\TenancyReviewSolicitation;
use App\Entity\User;
use App\Exception\DeveloperException;
use App\Service\EmailService;
use Symfony\Component\HttpFoundation\RequestStack;

class SendService
{
    private string $baseUrl;

    public function __construct(
        RequestStack $requestStack,
        private EmailService $emailService,
    ) {
        $currentRequest = $requestStack->getCurrentRequest();
        $this->baseUrl = null === $currentRequest
            ? 'https://homecomb.co.uk/'
            : $currentRequest->getSchemeAndHttpHost();
    }

    public function send(
        TenancyReviewSolicitation $tenancyReviewSolicitation,
        ?User $senderUser = null
    ): void {
        $url = $this->baseUrl.'/review-your-tenancy/'.$tenancyReviewSolicitation->getCode();

        $branch = $tenancyReviewSolicitation->getBranch();
        $agency = $branch->getAgency();
        if (null === $agency) {
            throw new DeveloperException('Unable to send Review Solicitation for branch with no Agency.');
        }

        $firstName = $tenancyReviewSolicitation->getRecipientFirstName();
        $lastName = $tenancyReviewSolicitation->getRecipientLastName();
        $addressLine1 = $tenancyReviewSolicitation->getProperty()->getAddressLine1();
        $agencyName = $agency->getName();

        $this->emailService->process(
            $tenancyReviewSolicitation->getRecipientEmail(),
            $firstName.' '.$lastName,
            'Please review your tenancy at '.$addressLine1.' with '.$agencyName,
            'review-solicitation',
            [
                'url' => $url,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'addressLine1' => $addressLine1,
                'agencyName' => $agencyName,
            ],
            null,
            $senderUser
        );

        // TODO record recipient user, type
    }
}
