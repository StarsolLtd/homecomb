<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\TenancyReview;
use App\Factory\AgencyAdminFactory;
use App\Factory\FlatModelFactory;
use App\Factory\TenancyReviewFactory;
use App\Model\Agency\Flat as FlatAgency;
use App\Model\Branch\Flat as FlatBranch;
use App\Model\TenancyReview\View;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class AdminAgencyFactoryTest extends TestCase
{
    use ProphecyTrait;

    private AgencyAdminFactory $adminAgencyFactory;

    private ObjectProphecy $flatModelFactory;
    private ObjectProphecy $reviewFactory;

    public function setUp(): void
    {
        $this->flatModelFactory = $this->prophesize(FlatModelFactory::class);
        $this->reviewFactory = $this->prophesize(TenancyReviewFactory::class);

        $this->adminAgencyFactory = new AgencyAdminFactory(
            $this->flatModelFactory->reveal(),
            $this->reviewFactory->reveal(),
        );
    }

    public function testCreateHome(): void
    {
        $review1 = (new TenancyReview())->setAuthor('Anna')->setPublished(true);
        $review2 = (new TenancyReview())->setAuthor('Beatrice')->setPublished(true);
        $review3 = (new TenancyReview())->setAuthor('Chloe')->setPublished(true);
        $review4 = (new TenancyReview())->setAuthor('Dora')->setPublished(true);
        $review5 = (new TenancyReview())->setAuthor('Eleanor')->setPublished(true);

        $review1View = $this->prophesize(View::class);
        $review2View = $this->prophesize(View::class);
        $review3View = $this->prophesize(View::class);
        $review4View = $this->prophesize(View::class);
        $review5View = $this->prophesize(View::class);

        $branch1 = (new Branch())->setName('Dereham')->addTenancyReview($review1)->addTenancyReview($review2)->addTenancyReview($review3);
        $branch2 = (new Branch())->setName('Swaffham')->addTenancyReview($review4)->addTenancyReview($review5);
        $branch3 = (new Branch())->setName('Bawdeswell');

        $branch1Model = $this->prophesize(FlatBranch::class);
        $branch2Model = $this->prophesize(FlatBranch::class);
        $branch3Model = $this->prophesize(FlatBranch::class);

        $agency = (new Agency())->addBranch($branch1)->addBranch($branch2)->addBranch($branch3);
        $agencyModel = $this->prophesize(FlatAgency::class);

        $this->flatModelFactory->getAgencyFlatModel($agency)->shouldBeCalled()->willReturn($agencyModel);

        $this->flatModelFactory->getBranchFlatModel($branch1)->shouldBeCalled()->willReturn($branch1Model);
        $this->flatModelFactory->getBranchFlatModel($branch2)->shouldBeCalled()->willReturn($branch2Model);
        $this->flatModelFactory->getBranchFlatModel($branch3)->shouldBeCalled()->willReturn($branch3Model);

        $this->reviewFactory->createViewFromEntity($review1)->shouldBeCalled()->willReturn($review1View);
        $this->reviewFactory->createViewFromEntity($review2)->shouldBeCalled()->willReturn($review2View);
        $this->reviewFactory->createViewFromEntity($review3)->shouldBeCalled()->willReturn($review3View);
        $this->reviewFactory->createViewFromEntity($review4)->shouldBeCalled()->willReturn($review4View);
        $this->reviewFactory->createViewFromEntity($review5)->shouldBeCalled()->willReturn($review5View);

        $output = $this->adminAgencyFactory->getHome($agency);

        $this->assertEquals($agencyModel->reveal(), $output->getAgency());
        $this->assertCount(3, $output->getBranches());
        $this->assertCount(5, $output->getTenancyReviews());
    }
}
