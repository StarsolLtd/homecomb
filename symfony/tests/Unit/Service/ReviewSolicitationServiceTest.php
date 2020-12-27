<?php

namespace App\Tests\Unit\Service;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Property;
use App\Entity\ReviewSolicitation;
use App\Entity\User;
use App\Factory\ReviewSolicitationFactory;
use App\Model\ReviewSolicitation\CreateReviewSolicitationInput;
use App\Model\ReviewSolicitation\FormData;
use App\Service\ReviewSolicitationService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ReviewSolicitationServiceTest extends TestCase
{
    use ProphecyTrait;

    private ReviewSolicitationService $reviewSolicitationService;

    private $userService;
    private $reviewSolicitationFactory;
    private $entityManager;
    private $mailer;

    public function setUp(): void
    {
        $requestStack = $this->prophesize(RequestStack::class);
        $this->userService = $this->prophesize(UserService::class);
        $this->reviewSolicitationFactory = $this->prophesize(ReviewSolicitationFactory::class);
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->mailer = $this->prophesize(MailerInterface::class);

        $request = $this->prophesize(Request::class);
        $requestStack->getCurrentRequest()->willReturn($request);
        $request->getSchemeAndHttpHost()->willReturn('https://homecomb.co.uk/');

        $this->reviewSolicitationService = new ReviewSolicitationService(
            $requestStack->reveal(),
            $this->userService->reveal(),
            $this->reviewSolicitationFactory->reveal(),
            $this->entityManager->reveal(),
            $this->mailer->reveal()
        );
    }

    public function testCreateAndSend(): void
    {
        $input = new CreateReviewSolicitationInput(
            'branchslug',
            'propertyslug',
            null,
            'Jack',
            'Harper',
            'jack.harper@starsol.co.uk',
            'SAMPLE'
        );
        $user = new User();
        $agency = (new Agency())->setName('Dereham Residential');
        $branch = (new Branch())->setAgency($agency);
        $property = (new Property())->setAddressLine1('15 Salmon Street');
        $reviewSolicitation = (new ReviewSolicitation())
            ->setProperty($property)
            ->setBranch($branch)
            ->setCode('sample')
            ->setRecipientEmail('sample.tenant@starsol.co.uk');

        $this->userService->getEntityFromInterface($user)
            ->shouldBeCalledOnce()
            ->willReturn($user);

        $this->reviewSolicitationFactory->createEntityFromInput($input, $user)
            ->shouldBeCalledOnce()
            ->willReturn($reviewSolicitation);

        $this->entityManager->persist($reviewSolicitation)->shouldBeCalledOnce();
        $this->entityManager->flush()->shouldBeCalledOnce();
        $this->mailer->send(Argument::type(Email::class))->shouldBeCalledOnce();

        $output = $this->reviewSolicitationService->createAndSend($input, $user);

        $this->assertTrue($output->isSuccess());
    }

    public function testGetFormData(): void
    {
        $user = new User();
        $formData = $this->prophesize(FormData::class);

        $this->userService->getEntityFromInterface($user)->shouldBeCalledOnce()->willReturn($user);
        $this->reviewSolicitationFactory->createFormDataModelFromUser($user)->shouldBeCalledOnce()->willReturn($formData);

        $this->reviewSolicitationService->getFormData($user);
    }
}
