<?php

namespace App\Tests\Unit\Util;

use App\Entity\Review;
use App\Repository\PropertyRepository;
use App\Service\AgencyService;
use App\Service\BranchService;
use App\Service\NotificationService;
use App\Service\ReviewService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ReviewServiceTest extends TestCase
{
    use ProphecyTrait;

    private ReviewService $reviewService;

    public function setUp(): void
    {
        $this->reviewService = new ReviewService(
            $this->prophesize(AgencyService::class)->reveal(),
            $this->prophesize(BranchService::class)->reveal(),
            $this->prophesize(NotificationService::class)->reveal(),
            $this->prophesize(EntityManager::class)->reveal(),
            $this->prophesize(PropertyRepository::class)->reveal(),
        );
    }

    public function testPublishReview(): void
    {
        $review = (new Review())->setPublished(false);

        $this->reviewService->publishReview($review);

        $this->assertTrue($review->isPublished());
    }
}
