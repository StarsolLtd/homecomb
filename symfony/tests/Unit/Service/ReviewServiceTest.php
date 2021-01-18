<?php

namespace App\Tests\Unit\Service;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Locale;
use App\Entity\Postcode;
use App\Entity\Property;
use App\Entity\Review;
use App\Entity\User;
use App\Factory\ReviewFactory;
use App\Model\Interaction\RequestDetails;
use App\Model\Review\Group;
use App\Model\Review\View;
use App\Model\SubmitReviewInput;
use App\Repository\PostcodeRepository;
use App\Repository\PropertyRepository;
use App\Repository\ReviewRepository;
use App\Service\AgencyService;
use App\Service\BranchService;
use App\Service\InteractionService;
use App\Service\NotificationService;
use App\Service\ReviewService;
use App\Service\ReviewSolicitationService;
use App\Service\UserService;
use App\Tests\Unit\EntityManagerTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Service\ReviewService
 */
class ReviewServiceTest extends TestCase
{
    use ProphecyTrait;
    use EntityManagerTrait;

    private ReviewService $reviewService;

    private $agencyService;
    private $branchService;
    private $interactionService;
    private $notificationService;
    private $reviewSolicitationService;
    private $userService;
    private $entityManager;
    private $postcodeRepository;
    private $propertyRepository;
    private $reviewRepository;
    private $reviewFactory;

    public function setUp(): void
    {
        $this->agencyService = $this->prophesize(AgencyService::class);
        $this->branchService = $this->prophesize(BranchService::class);
        $this->interactionService = $this->prophesize(InteractionService::class);
        $this->notificationService = $this->prophesize(NotificationService::class);
        $this->reviewSolicitationService = $this->prophesize(ReviewSolicitationService::class);
        $this->userService = $this->prophesize(UserService::class);
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->postcodeRepository = $this->prophesize(PostcodeRepository::class);
        $this->propertyRepository = $this->prophesize(PropertyRepository::class);
        $this->reviewRepository = $this->prophesize(ReviewRepository::class);
        $this->reviewFactory = $this->prophesize(ReviewFactory::class);

        $this->reviewService = new ReviewService(
            $this->agencyService->reveal(),
            $this->branchService->reveal(),
            $this->interactionService->reveal(),
            $this->notificationService->reveal(),
            $this->reviewSolicitationService->reveal(),
            $this->userService->reveal(),
            $this->entityManager->reveal(),
            $this->postcodeRepository->reveal(),
            $this->propertyRepository->reveal(),
            $this->reviewRepository->reveal(),
            $this->reviewFactory->reveal(),
        );
    }

    /**
     * @covers \App\Service\ReviewService::publishReview
     */
    public function testPublishReview1(): void
    {
        $review = (new Review())->setPublished(false);

        $this->reviewService->publishReview($review);

        $this->assertTrue($review->isPublished());
    }

    /**
     * @covers \App\Service\ReviewService::generateLocales
     */
    public function testGenerateLocales1(): void
    {
        $property = (new Property())->setPostcode('NR2 4SF');
        $review = (new Review())->setProperty($property);
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

        $locales = $this->reviewService->generateLocales($review);

        $this->assertEquals($locale, $locales->first());
    }

    /**
     * @covers \App\Service\ReviewService::generateLocales
     */
    public function testGenerateLocales2(): void
    {
        $property = (new Property())->setPostcode('');
        $review = (new Review())->setProperty($property);

        $locales = $this->reviewService->generateLocales($review);

        $this->assertEmpty($locales);
    }

    /**
     * @covers \App\Service\ReviewService::submitReview
     */
    public function testSubmitReview1(): void
    {
        $input = $this->prophesize(SubmitReviewInput::class);
        $user = $this->prophesize(User::class);
        $property = $this->prophesize(Property::class);
        $agency = $this->prophesize(Agency::class);
        $branch = $this->prophesize(Branch::class);
        $requestDetails = $this->prophesize(RequestDetails::class);
        $review = $this->prophesize(Review::class);

        $input->getPropertySlug()->shouldBeCalledOnce()->willReturn('propertyslug');
        $input->getAgencyName()->shouldBeCalledOnce()->willReturn('Test Agency Name');
        $input->getAgencyBranch()->shouldBeCalledOnce()->willReturn('Testerton');
        $input->getCode()->shouldBeCalledOnce()->willReturn('testcode');

        $this->propertyRepository->findOnePublishedBySlug('propertyslug')->shouldBeCalledOnce()->willReturn($property);

        $this->agencyService->findOrCreateByName('Test Agency Name')->shouldBeCalledOnce()->willReturn($agency);

        $this->branchService->findOrCreate('Testerton', $agency)->shouldBeCalledOnce()->willReturn($branch);

        $this->userService->getUserEntityOrNullFromUserInterface($user)->shouldBeCalledOnce()->willReturn($user);

        $this->reviewFactory->createEntity($input, $property, $branch, $user)
            ->shouldBeCalledOnce()
            ->willReturn($review)
        ;

        $this->reviewSolicitationService->complete('testcode', Argument::type(Review::class))->shouldBeCalledOnce();

        $this->assertEntitiesArePersistedAndFlush([$review]);

        $this->notificationService->sendReviewModerationNotification(Argument::type(Review::class))->shouldBeCalledOnce();

        $submitReviewOutput = $this->reviewService->submitReview(
            $input->reveal(),
            $user->reveal(),
            $requestDetails->reveal()
        );

        $this->assertEquals(true, $submitReviewOutput->isSuccess());
    }

    /**
     * @covers \App\Service\ReviewService::getViewById
     */
    public function testGetViewById1(): void
    {
        $entity = $this->prophesize(Review::class);
        $view = $this->prophesize(View::class);

        $this->reviewRepository->findOnePublishedById(56)
            ->shouldBeCalledOnce()
            ->willReturn($entity);

        $this->reviewFactory->createViewFromEntity($entity)
            ->shouldBeCalledOnce()
            ->willReturn($view->reveal());

        $output = $this->reviewService->getViewById(56);

        $this->assertEquals($view->reveal(), $output);
    }

    /**
     * @covers \App\Service\ReviewService::getLatestGroup
     */
    public function testGetLatest(): void
    {
        $reviews = [
            $this->prophesize(Review::class),
            $this->prophesize(Review::class),
            $this->prophesize(Review::class),
        ];
        $group = $this->prophesize(Group::class);

        $this->reviewRepository->findLatest(3)
            ->shouldBeCalledOnce()
            ->willReturn($reviews);

        $this->reviewFactory->createGroup('Latest Reviews', $reviews)
            ->shouldBeCalledOnce()
            ->willReturn($group);

        $this->assertEntityManagerUnused();

        $output = $this->reviewService->getLatestGroup();

        $this->assertEquals($group->reveal(), $output);
    }
}
