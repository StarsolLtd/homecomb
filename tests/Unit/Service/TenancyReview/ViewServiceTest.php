<?php

namespace App\Tests\Unit\Service\TenancyReview;

use App\Entity\TenancyReview;
use App\Factory\TenancyReviewFactory;
use App\Model\TenancyReview\Group;
use App\Model\TenancyReview\View;
use App\Repository\TenancyReviewRepository;
use App\Service\TenancyReview\ViewService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class ViewServiceTest extends TestCase
{
    use ProphecyTrait;

    private ViewService $viewService;

    private ObjectProphecy $reviewRepository;
    private ObjectProphecy $tenancyReviewFactory;

    public function setUp(): void
    {
        $this->reviewRepository = $this->prophesize(TenancyReviewRepository::class);
        $this->tenancyReviewFactory = $this->prophesize(TenancyReviewFactory::class);

        $this->viewService = new ViewService(
            $this->reviewRepository->reveal(),
            $this->tenancyReviewFactory->reveal(),
        );
    }

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

        $output = $this->viewService->getViewById(56);

        $this->assertEquals($view->reveal(), $output);
    }

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

        $output = $this->viewService->getLatestGroup();

        $this->assertEquals($group->reveal(), $output);
    }
}
