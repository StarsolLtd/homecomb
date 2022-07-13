<?php

namespace App\Tests\Unit\Service;

use App\Controller\Admin\BranchCrudController;
use App\Controller\Admin\FlagCrudController;
use App\Controller\Admin\LocaleReviewCrudController;
use App\Controller\Admin\TenancyReviewCrudController;
use App\Entity\Branch;
use App\Entity\Flag\Flag;
use App\Entity\Review\LocaleReview;
use App\Entity\TenancyReview;
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
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class NotificationServiceTest extends TestCase
{
    use ProphecyTrait;

    private NotificationService $notificationService;

    private ObjectProphecy $crudUrlGeneratorMock;
    private ObjectProphecy $loggerMock;
    private ObjectProphecy $mailerMock;
    private ObjectProphecy $userRepositoryMock;

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

    public function testLocaleSendReviewModerationNotification(): void
    {
        $LocaleReview = $this->prophesize(LocaleReview::class);
        $LocaleReview->getId()->shouldBeCalledOnce()->willReturn(42);

        $crudUrlBuilder = $this->prophesize(CrudUrlBuilder::class);

        $this->crudUrlGeneratorMock->build()->shouldBeCalledOnce()->willReturn($crudUrlBuilder);
        $crudUrlBuilder->setController(LocaleReviewCrudController::class)->shouldBeCalledOnce()->willReturn($crudUrlBuilder);
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

        $this->notificationService->sendLocaleReviewModerationNotification($LocaleReview->reveal());
    }

    public function testTenancySendReviewModerationNotification(): void
    {
        $tenancyReview = $this->prophesize(TenancyReview::class);
        $tenancyReview->getId()->shouldBeCalledOnce()->willReturn(42);

        $crudUrlBuilder = $this->prophesize(CrudUrlBuilder::class);

        $this->crudUrlGeneratorMock->build()->shouldBeCalledOnce()->willReturn($crudUrlBuilder);
        $crudUrlBuilder->setController(TenancyReviewCrudController::class)->shouldBeCalledOnce()->willReturn($crudUrlBuilder);
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

        $this->notificationService->sendTenancyReviewModerationNotification($tenancyReview->reveal());
    }

    public function testFlagReviewModerationNotification(): void
    {
        $flag = $this->prophesize(Flag::class);

        $flag->getId()->shouldBeCalledOnce()->willReturn(77);

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

        $this->notificationService->sendFlagModerationNotification($flag->reveal());
    }

    public function testSendBranchModerationNotification(): void
    {
        $branch = $this->prophesize(Branch::class);

        $branch->getId()->shouldBeCalledOnce()->willReturn(77);

        $crudUrlBuilder = $this->prophesize(CrudUrlBuilder::class);
        $this->crudUrlGeneratorMock->build()->shouldBeCalledOnce()->willReturn($crudUrlBuilder);
        $crudUrlBuilder->setController(BranchCrudController::class)->shouldBeCalledOnce()->willReturn($crudUrlBuilder);
        $crudUrlBuilder->setAction(Action::EDIT)->shouldBeCalledOnce()->willReturn($crudUrlBuilder);
        $crudUrlBuilder->setEntityId(77)->shouldBeCalledOnce()->willReturn($crudUrlBuilder);
        $crudUrlBuilder->generateUrl()->shouldBeCalledOnce()->willReturn('http://homecomb/test');

        $user = $this->prophesize(User::class);

        $this->userRepositoryMock
            ->findUsersWithRole('ROLE_MODERATOR')
            ->shouldBeCalledOnce()
            ->willReturn([$user]);

        $user->getEmail()->shouldBeCalledOnce()->willReturn('test@starsol.co.uk');

        $this->mailerMock->send(Argument::type(Email::class))->shouldBeCalledOnce();

        $this->loggerMock->info('Email sent to test@starsol.co.uk')->shouldBeCalledOnce();

        $this->notificationService->sendBranchModerationNotification($branch->reveal());
    }
}
