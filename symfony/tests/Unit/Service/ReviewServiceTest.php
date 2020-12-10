<?php

namespace App\Tests\Unit\Util;

use App\Entity\Review;
use App\Repository\PostcodeRepository;
use App\Repository\PropertyRepository;
use App\Service\AgencyService;
use App\Service\BranchService;
use App\Service\NotificationService;
use App\Service\ReviewService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ReviewServiceTest extends TestCase
{
    use ProphecyTrait;

    private ReviewService $reviewService;

    private $agencyServiceMock;
    private $branchServiceMock;
    private $notificationServiceMock;
    private $entityManagerMock;
    private $postcodeRepositoryMock;
    private $propertyRepositoryMock;

    public function setUp(): void
    {
        $this->agencyServiceMock = $this->prophesize(AgencyService::class);
        $this->branchServiceMock = $this->prophesize(BranchService::class);
        $this->notificationServiceMock = $this->prophesize(NotificationService::class);
        $this->entityManagerMock = $this->prophesize(EntityManagerInterface::class);
        $this->postcodeRepositoryMock = $this->prophesize(PostcodeRepository::class);
        $this->propertyRepositoryMock = $this->prophesize(PropertyRepository::class);

        $this->reviewService = new ReviewService(
            $this->agencyServiceMock->reveal(),
            $this->branchServiceMock->reveal(),
            $this->notificationServiceMock->reveal(),
            $this->entityManagerMock->reveal(),
            $this->postcodeRepositoryMock->reveal(),
            $this->propertyRepositoryMock->reveal(),
        );
    }

    public function testPublishReview(): void
    {
        $review = (new Review())->setPublished(false);

        $this->reviewService->publishReview($review);

        $this->assertTrue($review->isPublished());
    }
}
