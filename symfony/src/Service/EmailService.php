<?php

namespace App\Service;

use App\Entity\User;
use App\Factory\EmailFactory;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class EmailService
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;
    private MailerInterface $mailer;
    private EmailFactory $emailFactory;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        MailerInterface $mailer,
        EmailFactory $emailFactory
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->emailFactory = $emailFactory;
    }

    public function process(
        string $toEmail,
        string $toName,
        string $subject,
        string $templateFilename,
        array $context,
        ?int $type = null,
        ?User $senderUser = null,
        ?User $recipientUser = null
    ): void {
        $templatedEmail = (new TemplatedEmail())
            ->from(new Address('mailer@homecomb.co.uk', 'HomeComb'))
            ->to(new Address($toEmail, $toName))
            ->subject($subject)
            ->textTemplate('emails/'.$templateFilename.'.txt.twig')
            ->htmlTemplate('emails/'.$templateFilename.'.html.twig')
            ->context($context)
        ;

        $email = $this->emailFactory->createEntity(
            $templatedEmail->getFrom()[0]->getEncodedAddress(),
            $templatedEmail->getTo()[0]->getEncodedAddress(),
            (string) $templatedEmail->getSubject(),
            (string) $templatedEmail->getTextBody(), // TODO why is null?
            (string) $templatedEmail->getHtmlBody(), // TODO why is null?
            $type,
            $senderUser,
            $recipientUser
        );

        $this->entityManager->persist($email);

        try {
            $this->mailer->send($templatedEmail);
            $email->setSentAt(new DateTime());
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Exception thrown sending email: '.$e->getMessage());
        }

        $this->entityManager->flush();
    }
}
