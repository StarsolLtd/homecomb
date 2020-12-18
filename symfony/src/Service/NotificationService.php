<?php

namespace App\Service;

use App\Controller\Admin\AgencyCrudController;
use App\Controller\Admin\FlagCrudController;
use App\Controller\Admin\ReviewCrudController;
use App\Entity\Agency;
use App\Entity\Flag;
use App\Entity\Review;
use App\Entity\User;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class NotificationService
{
    private CrudUrlGenerator $crudUrlGenerator;
    private LoggerInterface $logger;
    private MailerInterface $mailer;
    private UserRepository $userRepository;

    public function __construct(
        CrudUrlGenerator $crudUrlGenerator,
        LoggerInterface $appLogger,
        MailerInterface $mailer,
        UserRepository $userRepository
    ) {
        $this->crudUrlGenerator = $crudUrlGenerator;
        $this->mailer = $mailer;
        $this->logger = $appLogger;
        $this->userRepository = $userRepository;
    }

    public function sendReviewModerationNotification(Review $review): void
    {
        $url = $this->crudUrlGenerator
            ->build()
            ->setController(ReviewCrudController::class)
            ->setAction(Action::EDIT)
            ->setEntityId($review->getId())
            ->generateUrl();

        $moderators = $this->userRepository->findUsersWithRole('ROLE_MODERATOR');

        foreach ($moderators as $moderator) {
            $this->notifyModerator($moderator, 'New review added to HomeComb', 'Go to '.$url.' to moderate.');
        }
    }

    public function sendFlagModerationNotification(Flag $flag): void
    {
        try {
            $url = $this->crudUrlGenerator
                ->build()
                ->setController(FlagCrudController::class)
                ->setAction(Action::EDIT)
                ->setEntityId($flag->getId())
                ->generateUrl();

            $moderators = $this->userRepository->findUsersWithRole('ROLE_MODERATOR');

            foreach ($moderators as $moderator) {
                $this->notifyModerator($moderator, 'New flag on HomeComb', 'Go to '.$url.' to moderate.');
            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    public function sendAgencyModerationNotification(Agency $agency): void
    {
        try {
            $url = $this->crudUrlGenerator
                ->build()
                ->setController(AgencyCrudController::class)
                ->setAction(Action::EDIT)
                ->setEntityId($agency->getId())
                ->generateUrl();

            $moderators = $this->userRepository->findUsersWithRole('ROLE_MODERATOR');

            foreach ($moderators as $moderator) {
                $this->notifyModerator($moderator, 'New agency on HomeComb', 'Go to '.$url.' to moderate.');
            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    private function notifyModerator(User $moderator, string $subject, string $text): void
    {
        $to = $moderator->getEmail();

        $email = (new Email())
            ->from('mailer@homecomb.co.uk')
            ->to($to)
            ->subject($subject)
            ->text($text);

        $this->mailer->send($email);

        $this->logger->info('Email sent to '.$to);
    }
}
