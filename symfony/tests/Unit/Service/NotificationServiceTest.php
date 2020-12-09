<?php

namespace App\Tests\Unit\Util;

use App\Controller\Admin\FlagCrudController;
use App\Controller\Admin\ReviewCrudController;
use App\Entity\Flag;
use App\Entity\Review;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\NotificationService;
use DG\BypassFinals;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class NotificationServiceTest extends TestCase
{
    use ProphecyTrait;

    private NotificationService $notificationService;

    private $crudUrlGeneratorMock;
    private $loggerMock;
    private $mailerMock;
    private $userRepositoryMock;

    public function setUp(): void
    {
        BypassFinals::enable();

        $this->crudUrlGeneratorMock = $this->prophesize(CrudUrlGenerator::class);
        $this->loggerMock = $this->prophesize(LoggerInterface::class);
        $this->mailerMock = $this->prophesize(MailerInterface::class);
        $this->userRepositoryMock = $this->prophesize(UserRepository::class);

        $this->notificationService = new NotificationService(
            $this->crudUrlGeneratorMock->reveal(),
            $this->loggerMock->reveal(),
            $this->mailerMock->reveal(),
            $this->userRepositoryMock->reveal(),
        );
    }

    public function testSendReviewModerationNotification(): void
    {
        $review = (new Review())->setIdForTest(42);

        $crudUrlBuilder = $this->prophesize(CrudUrlBuilder::class);

        $this->crudUrlGeneratorMock->build()->shouldBeCalledOnce()->willReturn($crudUrlBuilder);
        $crudUrlBuilder->setController(ReviewCrudController::class)->shouldBeCalledOnce()->willReturn($crudUrlBuilder);
        $crudUrlBuilder->setAction(Action::EDIT)->shouldBeCalledOnce()->willReturn($crudUrlBuilder);
        $crudUrlBuilder->setEntityId(42)->shouldBeCalledOnce()->willReturn($crudUrlBuilder);
        $crudUrlBuilder->generateUrl()->shouldBeCalledOnce()->willReturn('http://homecomb/test');

        $this->userRepositoryMock
            ->findUsersWithRole('ROLE_MODERATOR')
            ->shouldBeCalledOnce()
            ->willReturn([
                (new User())->setEmail('gina@starsol.co.uk'),
            ]);

        $this->mailerMock->send(Argument::type(Email::class))->shouldBeCalledOnce();

        $this->loggerMock->info('Email sent to gina@starsol.co.uk')->shouldBeCalledOnce();

        $this->notificationService->sendReviewModerationNotification($review);
    }

    public function testFlagReviewModerationNotification(): void
    {
        $flag = (new Flag())->setIdForTest(77);

        $crudUrlBuilder = $this->prophesize(CrudUrlBuilder::class);

        $this->crudUrlGeneratorMock->build()->shouldBeCalledOnce()->willReturn($crudUrlBuilder);
        $crudUrlBuilder->setController(FlagCrudController::class)->shouldBeCalledOnce()->willReturn($crudUrlBuilder);
        $crudUrlBuilder->setAction(Action::EDIT)->shouldBeCalledOnce()->willReturn($crudUrlBuilder);
        $crudUrlBuilder->setEntityId(77)->shouldBeCalledOnce()->willReturn($crudUrlBuilder);
        $crudUrlBuilder->generateUrl()->shouldBeCalledOnce()->willReturn('http://homecomb/test');

        $this->userRepositoryMock
            ->findUsersWithRole('ROLE_MODERATOR')
            ->shouldBeCalledOnce()
            ->willReturn([
                (new User())->setEmail('gina@starsol.co.uk'),
            ]);

        $this->mailerMock->send(Argument::type(Email::class))->shouldBeCalledOnce();

        $this->loggerMock->info('Email sent to gina@starsol.co.uk')->shouldBeCalledOnce();

        $this->notificationService->sendFlagModerationNotification($flag);
    }
}
