<?php

namespace App\Service;

use App\Entity\ReviewSolicitation;
use App\Factory\ReviewSolicitationFactory;
use App\Model\ReviewSolicitation\CreateReviewSolicitationInput;
use App\Model\ReviewSolicitation\CreateReviewSolicitationOutput;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\User\UserInterface;

class ReviewSolicitationService
{
    private string $baseUrl;
    private UserService $userService;
    private ReviewSolicitationFactory $reviewSolicitationFactory;
    private EntityManagerInterface $entityManager;
    private MailerInterface $mailer;

    public function __construct(
        RequestStack $requestStack,
        UserService $userService,
        ReviewSolicitationFactory $reviewSolicitationFactory,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ) {
        $currentRequest = $requestStack->getCurrentRequest();
        if (null === $currentRequest) {
            throw new RuntimeException('No current request found.');
        }
        $this->baseUrl = $currentRequest->getSchemeAndHttpHost();
        $this->userService = $userService;
        $this->reviewSolicitationFactory = $reviewSolicitationFactory;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
    }

    public function createAndSend(CreateReviewSolicitationInput $input, ?UserInterface $user): CreateReviewSolicitationOutput
    {
        $user = $this->userService->getEntityFromInterface($user);

        $reviewSolicitation = $this->reviewSolicitationFactory->createEntityFromInput($input, $user);
        $this->entityManager->persist($reviewSolicitation);
        $this->entityManager->flush();

        $this->send($reviewSolicitation);

        return new CreateReviewSolicitationOutput(true);
    }

    private function send(ReviewSolicitation $reviewSolicitation): void
    {
        $url = $this->baseUrl.'/review-your-tenancy/'.$reviewSolicitation->getCode();

        $branch = $reviewSolicitation->getBranch();
        $agency = $branch->getAgency();
        if (null === $agency) {
            $withCompany = $branch->getName();
        } else {
            $withCompany = $agency->getName();
        }

        $email = (new Email())
            ->from('mailer@homecomb.co.uk')
            ->to($reviewSolicitation->getRecipientEmail())
            ->subject('Please review your tenancy at '.$reviewSolicitation->getProperty()->getAddressLine1().' with '.$withCompany)
            ->text($url);

        $this->mailer->send($email);
    }
}
