<?php

namespace App\Tests\Unit\Service;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Locale;
use App\Entity\Postcode;
use App\Entity\Property;
use App\Entity\Review;
use App\Entity\User;
use App\Model\SubmitReviewInput;
use App\Repository\PostcodeRepository;
use App\Repository\PropertyRepository;
use App\Service\AgencyService;
use App\Service\BranchService;
use App\Service\NotificationService;
use App\Service\ReviewService;
use App\Service\UserService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class ReviewServiceTest extends TestCase
{
    use ProphecyTrait;

    private ReviewService $reviewService;

    private $agencyService;
    private $branchService;
    private $notificationService;
    private $userService;
    private $entityManager;
    private $postcodeRepository;
    private $propertyRepository;

    public function setUp(): void
    {
        $this->agencyService = $this->prophesize(AgencyService::class);
        $this->branchService = $this->prophesize(BranchService::class);
        $this->notificationService = $this->prophesize(NotificationService::class);
        $this->userService = $this->prophesize(UserService::class);
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->postcodeRepository = $this->prophesize(PostcodeRepository::class);
        $this->propertyRepository = $this->prophesize(PropertyRepository::class);

        $this->reviewService = new ReviewService(
            $this->agencyService->reveal(),
            $this->branchService->reveal(),
            $this->notificationService->reveal(),
            $this->userService->reveal(),
            $this->entityManager->reveal(),
            $this->postcodeRepository->reveal(),
            $this->propertyRepository->reveal(),
        );
    }

    public function testPublishReview(): void
    {
        $review = (new Review())->setPublished(false);

        $this->reviewService->publishReview($review);

        $this->assertTrue($review->isPublished());
    }

    public function testGenerateLocalesReturnsEmptyArrayWhenNoPropertyPostcode(): void
    {
        $property = (new Property())->setPostcode('');
        $review = (new Review())->setProperty($property);

        $locales = $this->reviewService->generateLocales($review);

        $this->assertEmpty($locales);
    }

    public function testGenerateLocales(): void
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

    public function testSubmitReview(): void
    {
        $reviewInput = new SubmitReviewInput(
            789,
            'Jo Smith',
            'jo.smith@starsol.co.uk',
            'Test Agency Name',
            'Testerton',
            'Nice tenancy',
            'It was pleasurable living here, except one time the pipes froze',
            4,
            4,
            4,
            5,
            null
        );

        $user = (new User());
        $property = (new Property());
        $agency = (new Agency());
        $branch = (new Branch());

        $this->propertyRepository->find(789)->shouldBeCalledOnce()->willReturn($property);
        $this->agencyService->findOrCreateByName('Test Agency Name')->shouldBeCalledOnce()->willReturn($agency);
        $this->branchService->findOrCreate('Testerton', $agency)->shouldBeCalledOnce()->willReturn($branch);
        $this->userService->getUserEntityOrNullFromUserInterface($user)->shouldBeCalledOnce()->willReturn($user);
        $this->entityManager->persist(Argument::type(Review::class))->shouldBeCalledOnce();
        $this->entityManager->flush()->shouldBeCalledOnce();
        $this->notificationService->sendReviewModerationNotification(Argument::type(Review::class))->shouldBeCalledOnce();

        $submitReviewOutput = $this->reviewService->submitReview($reviewInput, $user);

        $this->assertEquals(true, $submitReviewOutput->isSuccess());
    }
}
