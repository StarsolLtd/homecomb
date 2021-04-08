<?php

namespace App\Tests\Unit\Service;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Property;
use App\Entity\Review;
use App\Entity\ReviewSolicitation;
use App\Entity\User;
use App\Exception\DeveloperException;
use App\Exception\NotFoundException;
use App\Factory\ReviewSolicitationFactory;
use App\Model\ReviewSolicitation\CreateReviewSolicitationInput;
use App\Model\ReviewSolicitation\FormData;
use App\Model\ReviewSolicitation\View;
use App\Repository\ReviewSolicitationRepository;
use App\Service\EmailService;
use App\Service\ReviewSolicitationService;
use App\Service\UserService;
use App\Tests\Unit\EntityManagerTrait;
use App\Tests\Unit\UserEntityFromInterfaceTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\MailerInterface;

/**
 * @covers \App\Service\ReviewSolicitationService
 */
class ReviewSolicitationServiceTest extends TestCase
{
    use EntityManagerTrait;
    use ProphecyTrait;
    use UserEntityFromInterfaceTrait;

    private ReviewSolicitationService $reviewSolicitationService;

    private $emailService;
    private $userService;
    private $reviewSolicitationFactory;
    private $reviewSolicitationRepository;
    private $entityManager;
    private $logger;
    private $mailer;

    public function setUp(): void
    {
        $requestStack = $this->prophesize(RequestStack::class);
        $this->emailService = $this->prophesize(EmailService::class);
        $this->userService = $this->prophesize(UserService::class);
        $this->reviewSolicitationFactory = $this->prophesize(ReviewSolicitationFactory::class);
        $this->reviewSolicitationRepository = $this->prophesize(ReviewSolicitationRepository::class);
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->mailer = $this->prophesize(MailerInterface::class);

        $request = $this->prophesize(Request::class);
        $requestStack->getCurrentRequest()->willReturn($request);
        $request->getSchemeAndHttpHost()->willReturn('https://homecomb.co.uk/');

        $this->reviewSolicitationService = new ReviewSolicitationService(
            $requestStack->reveal(),
            $this->emailService->reveal(),
            $this->userService->reveal(),
            $this->reviewSolicitationFactory->reveal(),
            $this->reviewSolicitationRepository->reveal(),
            $this->entityManager->reveal(),
            $this->logger->reveal(),
            $this->mailer->reveal()
        );
    }

    /**
     * @covers \App\Service\ReviewSolicitationService::createAndSend
     */
    public function testCreateAndSend1(): void
    {
        $input = $this->getValidCreateReviewSolicitationInput();
        $user = $this->prophesize(User::class);
        $agency = $this->prophesize(Agency::class);
        $branch = $this->prophesize(Branch::class);
        $property = $this->prophesize(Property::class);

        $reviewSolicitation = $this->prophesize(ReviewSolicitation::class);

        $branch->getAgency()
            ->shouldBeCalledOnce()
            ->willReturn($agency);

        $agency->getName()
            ->shouldBeCalledOnce()
            ->willReturn('Dereham Residential');

        $reviewSolicitation->getCode()
            ->shouldBeCalledOnce()
            ->willReturn('testcode');

        $reviewSolicitation->getRecipientFirstName()
            ->shouldBeCalledOnce()
            ->willReturn('Jack');

        $reviewSolicitation->getRecipientLastName()
            ->shouldBeCalledOnce()
            ->willReturn('Parnell');

        $reviewSolicitation->getBranch()
            ->shouldBeCalledOnce()
            ->willReturn($branch);

        $reviewSolicitation->getProperty()
            ->shouldBeCalledOnce()
            ->willReturn($property);

        $reviewSolicitation->getRecipientEmail()
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

        $this->reviewSolicitationFactory->createEntityFromInput($input, $user)
            ->shouldBeCalledOnce()
            ->willReturn($reviewSolicitation);

        $this->assertEntitiesArePersistedAndFlush([$reviewSolicitation]);

        $output = $this->reviewSolicitationService->createAndSend($input, $user->reveal());

        $this->assertTrue($output->isSuccess());
    }

    /**
     * @covers \App\Service\ReviewSolicitationService::createAndSend
     * Test throws DeveloperException when Branch has no Agency
     */
    public function testCreateAndSend2(): void
    {
        $input = $this->getValidCreateReviewSolicitationInput();
        $user = new User();
        $branch = (new Branch());
        $property = (new Property())->setAddressLine1('15 Salmon Street');
        $reviewSolicitation = (new ReviewSolicitation())
            ->setProperty($property)
            ->setBranch($branch)
            ->setCode('sample')
            ->setRecipientEmail('sample.tenant@starsol.co.uk');

        $this->assertGetUserEntityFromInterface($user);

        $this->reviewSolicitationFactory->createEntityFromInput($input, $user)
            ->shouldBeCalledOnce()
            ->willReturn($reviewSolicitation);

        $this->assertEntitiesArePersistedAndFlush([$reviewSolicitation]);

        $this->expectException(DeveloperException::class);

        $this->mailer->send(Argument::any())->shouldNotBeCalled();

        $output = $this->reviewSolicitationService->createAndSend($input, $user);

        $this->assertTrue($output->isSuccess());
    }

    /**
     * @covers \App\Service\ReviewSolicitationService::getFormData
     */
    public function testGetFormData1(): void
    {
        $user = new User();
        $formData = $this->prophesize(FormData::class);

        $this->userService->getEntityFromInterface($user)->shouldBeCalledOnce()->willReturn($user);
        $this->reviewSolicitationFactory->createFormDataModelFromUser($user)->shouldBeCalledOnce()->willReturn($formData);

        $this->reviewSolicitationService->getFormData($user);
    }

    /**
     * @covers \App\Service\ReviewSolicitationService::getViewByCode
     */
    public function testGetViewByCode1(): void
    {
        $rs = (new ReviewSolicitation());
        $view = $this->prophesize(View::class);

        $this->reviewSolicitationRepository->findOneUnfinishedByCode('testcode')
            ->shouldBeCalledOnce()
            ->willReturn($rs);

        $this->reviewSolicitationFactory->createViewByEntity($rs)
            ->shouldBeCalledOnce()
            ->willReturn($view);

        $this->reviewSolicitationService->getViewByCode('testcode');
    }

    /**
     * @covers \App\Service\ReviewSolicitationService::complete
     */
    public function testComplete1(): void
    {
        $rs = (new ReviewSolicitation());
        $review = (new Review());
        $this->reviewSolicitationRepository->findOneUnfinishedByCode('testcode')
            ->shouldBeCalledOnce()
            ->willReturn($rs);

        $this->reviewSolicitationService->complete('testcode', $review);

        $this->assertEquals($review, $rs->getReview());
    }

    /**
     * @covers \App\Service\ReviewSolicitationService::complete
     * Test logs error when not found.
     */
    public function testComplete2(): void
    {
        $review = (new Review());

        $this->reviewSolicitationRepository->findOneUnfinishedByCode('testcode')
            ->shouldBeCalledOnce()
            ->willThrow(NotFoundException::class);

        $this->logger->error(Argument::type('string'))
            ->shouldBeCalledOnce();

        $this->reviewSolicitationService->complete('testcode', $review);
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
