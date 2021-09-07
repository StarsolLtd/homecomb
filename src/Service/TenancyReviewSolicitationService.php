<?php

namespace App\Service;

use App\Entity\TenancyReview;
use App\Entity\TenancyReviewSolicitation;
use App\Entity\User;
use App\Exception\DeveloperException;
use App\Exception\NotFoundException;
use App\Factory\TenancyReviewSolicitationFactory;
use App\Model\TenancyReviewSolicitation\CreateReviewSolicitationInput;
use App\Model\TenancyReviewSolicitation\CreateReviewSolicitationOutput;
use App\Model\TenancyReviewSolicitation\FormData;
use App\Model\TenancyReviewSolicitation\View;
use App\Repository\TenancyReviewSolicitationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;

class TenancyReviewSolicitationService
{
    private string $baseUrl;

    public function __construct(
        RequestStack $requestStack,
        private EmailService $emailService,
        private UserService $userService,
        private TenancyReviewSolicitationFactory $tenancyReviewSolicitationFactory,
        private TenancyReviewSolicitationRepository $tenancyReviewSolicitationRepository,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
    ) {
        $currentRequest = $requestStack->getCurrentRequest();
        if (null === $currentRequest) {
            $this->baseUrl = 'https://homecomb.co.uk/';
        } else {
            $this->baseUrl = $currentRequest->getSchemeAndHttpHost();
        }
    }

    public function getFormData(?UserInterface $user): FormData
    {
        $user = $this->userService->getEntityFromInterface($user);

        return $this->tenancyReviewSolicitationFactory->createFormDataModelFromUser($user);
    }

    public function createAndSend(CreateReviewSolicitationInput $input, ?UserInterface $user): CreateReviewSolicitationOutput
    {
        $user = $this->userService->getEntityFromInterface($user);

        $tenancyReviewSolicitation = $this->tenancyReviewSolicitationFactory->createEntityFromInput($input, $user);
        $this->entityManager->persist($tenancyReviewSolicitation);
        $this->entityManager->flush();

        $this->send($tenancyReviewSolicitation, $user);

        return new CreateReviewSolicitationOutput(true);
    }

    public function getViewByCode(string $code): View
    {
        $rs = $this->tenancyReviewSolicitationRepository->findOneUnfinishedByCode($code);

        return $this->tenancyReviewSolicitationFactory->createViewByEntity($rs);
    }

    public function complete(string $code, TenancyReview $tenancyReview): void
    {
        try {
            $rs = $this->tenancyReviewSolicitationRepository->findOneUnfinishedByCode($code);
            $rs->setTenancyReview($tenancyReview);
        } catch (NotFoundException $e) {
            $this->logger->error('Exception thrown completing TenancyReviewSolicitation: '.$e->getMessage());
        }
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
