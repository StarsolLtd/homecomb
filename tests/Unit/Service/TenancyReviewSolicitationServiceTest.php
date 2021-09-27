<?php

namespace App\Tests\Unit\Service;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Property;
use App\Entity\TenancyReview;
use App\Entity\TenancyReviewSolicitation;
use App\Entity\User;
use App\Exception\DeveloperException;
use App\Exception\NotFoundException;
use App\Factory\TenancyReviewSolicitationFactory;
use App\Model\TenancyReviewSolicitation\CreateReviewSolicitationInput;
use App\Model\TenancyReviewSolicitation\FormData;
use App\Model\TenancyReviewSolicitation\View;
use App\Repository\TenancyReviewSolicitationRepository;
use App\Service\EmailService;
use App\Service\TenancyReviewSolicitationService;
use App\Service\UserService;
use App\Tests\Unit\EntityManagerTrait;
use App\Tests\Unit\UserEntityFromInterfaceTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\MailerInterface;

/**
 * @covers \App\Service\TenancyReviewSolicitationService
 */
final class TenancyReviewSolicitationServiceTest extends TestCase
{
    use EntityManagerTrait;
    use ProphecyTrait;
    use UserEntityFromInterfaceTrait;

    private TenancyReviewSolicitationService $tenancyReviewSolicitationService;

    private ObjectProphecy $emailService;
    private ObjectProphecy $userService;
    private ObjectProphecy $tenancyReviewSolicitationFactory;
    private ObjectProphecy $tenancyReviewSolicitationRepository;
    private ObjectProphecy $logger;
    private ObjectProphecy $mailer;

    public function setUp(): void
    {
        $requestStack = $this->prophesize(RequestStack::class);
        $this->emailService = $this->prophesize(EmailService::class);
        $this->userService = $this->prophesize(UserService::class);
        $this->tenancyReviewSolicitationFactory = $this->prophesize(TenancyReviewSolicitationFactory::class);
        $this->tenancyReviewSolicitationRepository = $this->prophesize(TenancyReviewSolicitationRepository::class);
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->mailer = $this->prophesize(MailerInterface::class);

        $request = $this->prophesize(Request::class);
        $requestStack->getCurrentRequest()->willReturn($request);
        $request->getSchemeAndHttpHost()->willReturn('https://homecomb.co.uk/');

        $this->tenancyReviewSolicitationService = new TenancyReviewSolicitationService(
            $requestStack->reveal(),
            $this->emailService->reveal(),
            $this->userService->reveal(),
            $this->tenancyReviewSolicitationFactory->reveal(),
            $this->tenancyReviewSolicitationRepository->reveal(),
            $this->entityManager->reveal(),
            $this->logger->reveal(),
            $this->mailer->reveal()
        );
    }

    /**
     * @covers \App\Service\TenancyReviewSolicitationService::createAndSend
     */
    public function testCreateAndSend1(): void
    {
        $input = $this->getValidCreateReviewSolicitationInput();
        $user = $this->prophesize(User::class);
        $agency = $this->prophesize(Agency::class);
        $branch = $this->prophesize(Branch::class);
        $property = $this->prophesize(Property::class);

        $tenancyReviewSolicitation = $this->prophesize(TenancyReviewSolicitation::class);

        $branch->getAgency()
            ->shouldBeCalledOnce()
            ->willReturn($agency);

        $agency->getName()
            ->shouldBeCalledOnce()
            ->willReturn('Dereham Residential');

        $tenancyReviewSolicitation->getCode()
            ->shouldBeCalledOnce()
            ->willReturn('testcode');

        $tenancyReviewSolicitation->getRecipientFirstName()
            ->shouldBeCalledOnce()
            ->willReturn('Jack');

        $tenancyReviewSolicitation->getRecipientLastName()
            ->shouldBeCalledOnce()
            ->willReturn('Parnell');

        $tenancyReviewSolicitation->getBranch()
            ->shouldBeCalledOnce()
            ->willReturn($branch);

        $tenancyReviewSolicitation->getProperty()
            ->shouldBeCalledOnce()
            ->willReturn($property);

        $tenancyReviewSolicitation->getRecipientEmail()
            ->shouldBeCalledOnce()
            ->willReturn('sample.tenant@starsol.co.uk');

        $property->getAddressLine1()
            ->shouldBeCalledOnce()
            ->willReturn('15 Salmon Street');

        $this->emailService->process(
            'sample.tenant@starsol.co.uk',
            'Jack Parnell',
            'Please review your tenancy at 15 Salmon Street with Dereham Residential',
            'review-solicitation',
            Argument::type('array'),
            null,
            $user->reveal()
        )->shouldBeCalledOnce();

        $this->assertGetUserEntityFromInterface($user);

        $this->tenancyReviewSolicitationFactory->createEntityFromInput($input, $user)
            ->shouldBeCalledOnce()
            ->willReturn($tenancyReviewSolicitation);

        $this->assertEntitiesArePersistedAndFlush([$tenancyReviewSolicitation]);

        $output = $this->tenancyReviewSolicitationService->createAndSend($input, $user->reveal());

        $this->assertTrue($output->isSuccess());
    }

    /**
     * @covers \App\Service\TenancyReviewSolicitationService::createAndSend
     * Test throws DeveloperException when Branch has no Agency
     */
    public function testCreateAndSend2(): void
    {
        $input = $this->getValidCreateReviewSolicitationInput();
        $user = new User();
        $branch = (new Branch());
        $property = (new Property())->setAddressLine1('15 Salmon Street');
        $tenancyReviewSolicitation = (new TenancyReviewSolicitation())
            ->setProperty($property)
            ->setBranch($branch)
            ->setCode('sample')
            ->setRecipientEmail('sample.tenant@starsol.co.uk');

        $this->assertGetUserEntityFromInterface($user);

        $this->tenancyReviewSolicitationFactory->createEntityFromInput($input, $user)
            ->shouldBeCalledOnce()
            ->willReturn($tenancyReviewSolicitation);

        $this->assertEntitiesArePersistedAndFlush([$tenancyReviewSolicitation]);

        $this->expectException(DeveloperException::class);

        $this->mailer->send(Argument::any())->shouldNotBeCalled();

        $output = $this->tenancyReviewSolicitationService->createAndSend($input, $user);

        $this->assertTrue($output->isSuccess());
    }

    /**
     * @covers \App\Service\TenancyReviewSolicitationService::getFormData
     */
    public function testGetFormData1(): void
    {
        $user = new User();
        $formData = $this->prophesize(FormData::class);

        $this->userService->getEntityFromInterface($user)->shouldBeCalledOnce()->willReturn($user);
        $this->tenancyReviewSolicitationFactory->createFormDataModelFromUser($user)->shouldBeCalledOnce()->willReturn($formData);

        $this->tenancyReviewSolicitationService->getFormData($user);
    }

    /**
     * @covers \App\Service\TenancyReviewSolicitationService::getViewByCode
     */
    public function testGetViewByCode1(): void
    {
        $rs = (new TenancyReviewSolicitation());
        $view = $this->prophesize(View::class);

        $this->tenancyReviewSolicitationRepository->findOneUnfinishedByCode('testcode')
            ->shouldBeCalledOnce()
            ->willReturn($rs);

        $this->tenancyReviewSolicitationFactory->createViewByEntity($rs)
            ->shouldBeCalledOnce()
            ->willReturn($view);

        $this->tenancyReviewSolicitationService->getViewByCode('testcode');
    }

    /**
     * @covers \App\Service\TenancyReviewSolicitationService::complete
     */
    public function testComplete1(): void
    {
        $rs = (new TenancyReviewSolicitation());
        $tenancyReview = (new TenancyReview());
        $this->tenancyReviewSolicitationRepository->findOneUnfinishedByCode('testcode')
            ->shouldBeCalledOnce()
            ->willReturn($rs);

        $this->tenancyReviewSolicitationService->complete('testcode', $tenancyReview);

        $this->assertEquals($tenancyReview, $rs->getTenancyReview());
    }

    /**
     * @covers \App\Service\TenancyReviewSolicitationService::complete
     * Test logs error when not found.
     */
    public function testComplete2(): void
    {
        $tenancyReview = (new TenancyReview());

        $this->tenancyReviewSolicitationRepository->findOneUnfinishedByCode('testcode')
            ->shouldBeCalledOnce()
            ->willThrow(NotFoundException::class);

        $this->logger->error(Argument::type('string'))
            ->shouldBeCalledOnce();

        $this->tenancyReviewSolicitationService->complete('testcode', $tenancyReview);
    }

    private function getValidCreateReviewSolicitationInput(): CreateReviewSolicitationInput
    {
        return new CreateReviewSolicitationInput(
            'branchslug',
            'propertyslug',
            null,
            'Jack',
            'Harper',
            'jack.harper@starsol.co.uk',
            'SAMPLE'
        );
    }
}
