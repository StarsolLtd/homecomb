<?php

namespace App\Tests\Unit\Util;

use App\Entity\Locale;
use App\Entity\Postcode;
use App\Entity\Property;
use App\Entity\Review;
use App\Repository\PostcodeRepository;
use App\Repository\PropertyRepository;
use App\Service\AgencyService;
use App\Service\BranchService;
use App\Service\NotificationService;
use App\Service\ReviewService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ReviewServiceTest extends TestCase
{
    use ProphecyTrait;

    private ReviewService $reviewService;

    private $agencyService;
    private $branchService;
    private $notificationService;
    private $entityManager;
    private $postcodeRepository;
    private $propertyRepository;

    public function setUp(): void
    {
        $this->agencyService = $this->prophesize(AgencyService::class);
        $this->branchService = $this->prophesize(BranchService::class);
        $this->notificationService = $this->prophesize(NotificationService::class);
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->postcodeRepository = $this->prophesize(PostcodeRepository::class);
        $this->propertyRepository = $this->prophesize(PropertyRepository::class);

        $this->reviewService = new ReviewService(
            $this->agencyService->reveal(),
            $this->branchService->reveal(),
            $this->notificationService->reveal(),
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
}
