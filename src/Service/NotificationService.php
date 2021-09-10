<?php

namespace App\Service;

use App\Controller\Admin\AgencyCrudController;
use App\Controller\Admin\BranchCrudController;
use App\Controller\Admin\FlagCrudController;
use App\Controller\Admin\LocaleReviewCrudController;
use App\Controller\Admin\TenancyReviewCrudController;
use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Flag\Flag;
use App\Entity\Review\LocaleReview;
use App\Entity\TenancyReview;
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
    private LoggerInterface $logger;

    public function __construct(
        private CrudUrlGenerator $crudUrlGenerator,
        LoggerInterface $appLogger,
        private MailerInterface $mailer,
        private UserRepository $userRepository
    ) {
        $this->logger = $appLogger;
    }

    public function sendLocaleReviewModerationNotification(LocaleReview $localeReview): void
    {
        $url = $this->crudUrlGenerator
            ->build()
            ->setController(LocaleReviewCrudController::class)
            ->setAction(Action::EDIT)
            ->setEntityId($localeReview->getId())
            ->generateUrl();

        $moderators = $this->userRepository->findUsersWithRole('ROLE_MODERATOR');

        foreach ($moderators as $moderator) {
            $this->notifyModerator($moderator, 'New locale review added to HomeComb', 'Go to '.$url.' to moderate.');
        }
    }

    public function sendTenancyReviewModerationNotification(TenancyReview $tenancyReview): void
    {
        $url = $this->crudUrlGenerator
            ->build()
            ->setController(TenancyReviewCrudController::class)
            ->setAction(Action::EDIT)
            ->setEntityId($tenancyReview->getId())
            ->generateUrl();

        $moderators = $this->userRepository->findUsersWithRole('ROLE_MODERATOR');

        foreach ($moderators as $moderator) {
            $this->notifyModerator($moderator, 'New tenancy review added to HomeComb', 'Go to '.$url.' to moderate.');
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

    public function sendBranchModerationNotification(Branch $branch): void
    {
        try {
            $url = $this->crudUrlGenerator
                ->build()
                ->setController(BranchCrudController::class)
                ->setAction(Action::EDIT)
                ->setEntityId($branch->getId())
                ->generateUrl();

            $moderators = $this->userRepository->findUsersWithRole('ROLE_MODERATOR');

            foreach ($moderators as $moderator) {
                $this->notifyModerator($moderator, 'New branch on HomeComb', 'Go to '.$url.' to moderate.');
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
