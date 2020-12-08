<?php

namespace App\Service;

use App\Controller\Admin\ReviewCrudController;
use App\Entity\Review;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class NotificationService
{
    private CrudUrlGenerator $crudUrlGenerator;
    private MailerInterface $mailer;
    private UserRepository $userRepository;

    public function __construct(
        CrudUrlGenerator $crudUrlGenerator,
        MailerInterface $mailer,
        UserRepository $userRepository
    ) {
        $this->crudUrlGenerator = $crudUrlGenerator;
        $this->mailer = $mailer;
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
            $email = (new Email())
                ->from('mailer@homecomb.co.uk')
                ->to($moderator->getEmail())
                ->subject('New review added to HomeComb')
                ->text('Go to '.$url.' to moderate.');

            $this->mailer->send($email);
        }
    }
}
