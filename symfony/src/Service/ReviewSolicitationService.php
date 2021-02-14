<?php

namespace App\Service;

use App\Entity\Review;
use App\Entity\ReviewSolicitation;
use App\Exception\DeveloperException;
use App\Exception\NotFoundException;
use App\Factory\ReviewSolicitationFactory;
use App\Model\ReviewSolicitation\CreateReviewSolicitationInput;
use App\Model\ReviewSolicitation\CreateReviewSolicitationOutput;
use App\Model\ReviewSolicitation\FormData;
use App\Model\ReviewSolicitation\View;
use App\Repository\ReviewSolicitationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\User\UserInterface;

class ReviewSolicitationService
{
    private string $baseUrl;
    private UserService $userService;
    private ReviewSolicitationFactory $reviewSolicitationFactory;
    private ReviewSolicitationRepository $reviewSolicitationRepository;
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;
    private MailerInterface $mailer;

    public function __construct(
        RequestStack $requestStack,
        UserService $userService,
        ReviewSolicitationFactory $reviewSolicitationFactory,
        ReviewSolicitationRepository $reviewSolicitationRepository,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        MailerInterface $mailer
    ) {
        $currentRequest = $requestStack->getCurrentRequest();
        if (null === $currentRequest) {
            $this->baseUrl = 'https://homecomb.co.uk/';
        } else {
            $this->baseUrl = $currentRequest->getSchemeAndHttpHost();
        }
        $this->userService = $userService;
        $this->reviewSolicitationFactory = $reviewSolicitationFactory;
        $this->reviewSolicitationRepository = $reviewSolicitationRepository;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->mailer = $mailer;
    }

    public function getFormData(?UserInterface $user): FormData
    {
        $user = $this->userService->getEntityFromInterface($user);

        return $this->reviewSolicitationFactory->createFormDataModelFromUser($user);
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

    public function getViewByCode(string $code): View
    {
        $rs = $this->reviewSolicitationRepository->findOneUnfinishedByCode($code);

        return $this->reviewSolicitationFactory->createViewByEntity($rs);
    }

    public function complete(string $code, Review $review): void
    {
        try {
            $rs = $this->reviewSolicitationRepository->findOneUnfinishedByCode($code);
            $rs->setReview($review);
        } catch (NotFoundException $e) {
            $this->logger->error('Exception thrown completing ReviewSolicitation: '.$e->getMessage());
        }
    }

    private function send(ReviewSolicitation $reviewSolicitation): void
    {
        $url = $this->baseUrl.'/review-your-tenancy/'.$reviewSolicitation->getCode();

        $branch = $reviewSolicitation->getBranch();
        $agency = $branch->getAgency();
        if (null === $agency) {
            throw new DeveloperException('Unable to send Review Solicitation for branch with no Agency.');
        }

        $firstName = $reviewSolicitation->getRecipientFirstName();
        $lastName = $reviewSolicitation->getRecipientLastName();
        $addressLine1 = $reviewSolicitation->getProperty()->getAddressLine1();
        $agencyName = $agency->getName();

        $email = (new TemplatedEmail())
            ->from(new Address('mailer@homecomb.co.uk', 'HomeComb'))
            ->to(new Address($reviewSolicitation->getRecipientEmail(), $firstName.' '.$lastName))
            ->subject('Please review your tenancy at '.$addressLine1.' with '.$agencyName)
            ->text($url)
            ->htmlTemplate('emails/review-solicitation.html.twig')
            ->context(
                [
                    'url' => $url,
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'addressLine1' => $addressLine1,
                    'agencyName' => $agencyName,
                ]
            )
        ;

        $this->mailer->send($email);
    }
}
