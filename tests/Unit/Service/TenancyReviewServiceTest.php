<?php

namespace App\Tests\Unit\Service;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Locale\Locale;
use App\Entity\Postcode;
use App\Entity\Property;
use App\Entity\TenancyReview;
use App\Entity\User;
use App\Exception\UnexpectedValueException;
use App\Factory\TenancyReviewFactory;
use App\Model\Interaction\RequestDetails;
use App\Model\TenancyReview\Group;
use App\Model\TenancyReview\SubmitInput;
use App\Model\TenancyReview\View;
use App\Repository\PostcodeRepository;
use App\Repository\PropertyRepository;
use App\Repository\TenancyReviewRepository;
use App\Service\AgencyService;
use App\Service\BranchService;
use App\Service\InteractionService;
use App\Service\NotificationService;
use App\Service\TenancyReviewService;
use App\Service\TenancyReviewSolicitationService;
use App\Service\UserService;
use App\Tests\Unit\EntityManagerTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Service\TenancyReviewService
 */
class TenancyReviewServiceTest extends TestCase
{
    use ProphecyTrait;
    use EntityManagerTrait;

    private TenancyReviewService $tenancyReviewService;

    private ObjectProphecy $agencyService;
    private ObjectProphecy $branchService;
    private ObjectProphecy $interactionService;
    private ObjectProphecy $notificationService;
    private ObjectProphecy $tenancyReviewSolicitationService;
    private ObjectProphecy $userService;
    private ObjectProphecy $entityManager;
    private ObjectProphecy $postcodeRepository;
    private ObjectProphecy $propertyRepository;
    private ObjectProphecy $reviewRepository;
    private ObjectProphecy $tenancyReviewFactory;

    public function setUp(): void
    {
        $this->agencyService = $this->prophesize(AgencyService::class);
        $this->branchService = $this->prophesize(BranchService::class);
        $this->interactionService = $this->prophesize(InteractionService::class);
        $this->notificationService = $this->prophesize(NotificationService::class);
        $this->tenancyReviewSolicitationService = $this->prophesize(TenancyReviewSolicitationService::class);
        $this->userService = $this->prophesize(UserService::class);
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->postcodeRepository = $this->prophesize(PostcodeRepository::class);
        $this->propertyRepository = $this->prophesize(PropertyRepository::class);
        $this->reviewRepository = $this->prophesize(TenancyReviewRepository::class);
        $this->tenancyReviewFactory = $this->prophesize(TenancyReviewFactory::class);

        $this->tenancyReviewService = new TenancyReviewService(
            $this->agencyService->reveal(),
            $this->branchService->reveal(),
            $this->interactionService->reveal(),
            $this->notificationService->reveal(),
            $this->tenancyReviewSolicitationService->reveal(),
            $this->userService->reveal(),
            $this->entityManager->reveal(),
            $this->postcodeRepository->reveal(),
            $this->propertyRepository->reveal(),
            $this->reviewRepository->reveal(),
            $this->tenancyReviewFactory->reveal(),
        );
    }

    /**
     * @covers \App\Service\TenancyReviewService::publishReview
     */
    public function testPublishReview1(): void
    {
        $tenancyReview = (new TenancyReview())->setPublished(false);

        $this->tenancyReviewService->publishReview($tenancyReview);

        $this->assertTrue($tenancyReview->isPublished());
    }

    /**
     * @covers \App\Service\TenancyReviewService::generateLocales
     */
    public function testGenerateLocales1(): void
    {
        $property = (new Property())->setPostcode('NR2 4SF');
        $tenancyReview = (new TenancyReview())->setProperty($property);
        $postcode = (new Postcode())->setPostcode('NR2');
        $postcodeCollection = (new ArrayCollection());
        $postcodeCollection->add($postcode);
        $locale = (new Locale())->setName('Norwich')->addPostcode($postcode);

        $this->postcodeRepository
            ->findBeginningWith('NR2')
            ->shouldBeCalledOnce()
            ->willReturn($postcodeCollection);

        $this->entityManager
            ->flush()
            ->shouldBeCalledOnce();

        $locales = $this->tenancyReviewService->generateLocales($tenancyReview);

        $this->assertEquals($locale, $locales->first());
    }

    /**
     * @covers \App\Service\TenancyReviewService::generateLocales
     */
    public function testGenerateLocales2(): void
    {
        $property = (new Property())->setPostcode('');
        $tenancyReview = (new TenancyReview())->setProperty($property);

        $locales = $this->tenancyReviewService->generateLocales($tenancyReview);

        $this->assertEmpty($locales);
    }

    /**
     * @covers \App\Service\TenancyReviewService::submitReview
     * Test successful review submission.
     */
    public function testSubmitReview1(): void
    {
        $requestDetails = $this->prophesize(RequestDetails::class);

        list($input, $user) = $this->prophesizeSubmitReview();

        $this->interactionService->record('Review', 45, $requestDetails, $user)
            ->shouldBeCalledOnce();

        $submitOutput = $this->tenancyReviewService->submitReview(
            $input->reveal(),
            $user->reveal(),
            $requestDetails->reveal()
        );

        $this->assertEquals(true, $submitOutput->isSuccess());
    }

    /**
     * @covers \App\Service\TenancyReviewService::submitReview
     * Test catching of exception when thrown by InteractionService::record.
     */
    public function testSubmitReview2(): void
    {
        $requestDetails = $this->prophesize(RequestDetails::class);

        list($input, $user) = $this->prophesizeSubmitReview();

        $this->interactionService->record('Review', 45, $requestDetails, $user)
            ->shouldBeCalledOnce()
            ->willThrow(UnexpectedValueException::class);

        $submitOutput = $this->tenancyReviewService->submitReview(
            $input->reveal(),
            $user->reveal(),
            $requestDetails->reveal()
        );

        $this->assertEquals(true, $submitOutput->isSuccess());
    }

    /**
     * @covers \App\Service\TenancyReviewService::getViewById
     */
    public function testGetViewById1(): void
    {
        $entity = $this->prophesize(TenancyReview::class);
        $view = $this->prophesize(View::class);

        $this->reviewRepository->findOnePublishedById(56)
            ->shouldBeCalledOnce()
            ->willReturn($entity);

        $this->tenancyReviewFactory->createViewFromEntity($entity)
            ->shouldBeCalledOnce()
            ->willReturn($view->reveal());

        $output = $this->tenancyReviewService->getViewById(56);

        $this->assertEquals($view->reveal(), $output);
    }

    /**
     * @covers \App\Service\TenancyReviewService::getLatestGroup
     */
    public function testGetLatestGroup1(): void
    {
        $reviews = [
            $this->prophesize(TenancyReview::class),
            $this->prophesize(TenancyReview::class),
            $this->prophesize(TenancyReview::class),
        ];
        $group = $this->prophesize(Group::class);

        $this->reviewRepository->findLatest(3)
            ->shouldBeCalledOnce()
            ->willReturn($reviews);

        $this->tenancyReviewFactory->createGroup('Latest Reviews', $reviews)
            ->shouldBeCalledOnce()
            ->willReturn($group);

        $this->assertEntityManagerUnused();

        $output = $this->tenancyReviewService->getLatestGroup();

        $this->assertEquals($group->reveal(), $output);
    }

    private function prophesizeSubmitReview(): array
    {
        $input = $this->prophesize(SubmitInput::class);
        $user = $this->prophesize(User::class);
        $property = $this->prophesize(Property::class);
        $agency = $this->prophesize(Agency::class);
        $branch = $this->prophesize(Branch::class);
        $tenancyReview = $this->prophesize(TenancyReview::class);

        $input->getPropertySlug()->shouldBeCalledOnce()->willReturn('propertyslug');
        $input->getAgencyName()->shouldBeCalledOnce()->willReturn('Test Agency Name');
        $input->getAgencyBranch()->shouldBeCalledOnce()->willReturn('Testerton');
        $input->getCode()->shouldBeCalledOnce()->willReturn('testcode');

        $this->propertyRepository->findOnePublishedBySlug('propertyslug')->shouldBeCalledOnce()->willReturn($property);

        $this->agencyService->findOrCreateByName('Test Agency Name')->shouldBeCalledOnce()->willReturn($agency);

        $this->branchService->findOrCreate('Testerton', $agency)->shouldBeCalledOnce()->willReturn($branch);

        $this->userService->getUserEntityOrNullFromUserInterface($user)->shouldBeCalledOnce()->willReturn($user);

        $this->tenancyReviewFactory->createEntity($input, $property, $branch, $user)
            ->shouldBeCalledOnce()
            ->willReturn($tenancyReview)
        ;

        $this->tenancyReviewSolicitationService->complete('testcode', Argument::type(TenancyReview::class))->shouldBeCalledOnce();

        $this->assertEntitiesArePersistedAndFlush([$tenancyReview]);

        $this->notificationService->sendTenancyReviewModerationNotification(Argument::type(TenancyReview::class))->shouldBeCalledOnce();

        $tenancyReview->getId()
            ->shouldBeCalledOnce()
            ->willReturn(45);

        return [$input, $user];
    }
}
