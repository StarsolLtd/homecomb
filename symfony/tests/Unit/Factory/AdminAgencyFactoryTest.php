<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Review;
use App\Factory\AdminAgencyFactory;
use App\Factory\FlatModelFactory;
use App\Factory\ReviewFactory;
use App\Model\Agency\Flat as FlatAgency;
use App\Model\Branch\Flat as FlatBranch;
use App\Model\Review\View;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class AdminAgencyFactoryTest extends TestCase
{
    use ProphecyTrait;

    private AdminAgencyFactory $adminAgencyFactory;

    private $flatModelFactory;
    private $reviewFactory;

    public function setUp(): void
    {
        $this->flatModelFactory = $this->prophesize(FlatModelFactory::class);
        $this->reviewFactory = $this->prophesize(ReviewFactory::class);

        $this->adminAgencyFactory = new AdminAgencyFactory(
            $this->flatModelFactory->reveal(),
            $this->reviewFactory->reveal(),
        );
    }

    public function testCreateHome(): void
    {
        $review1 = (new Review())->setAuthor('Anna')->setPublished(true);
        $review2 = (new Review())->setAuthor('Beatrice')->setPublished(true);
        $review3 = (new Review())->setAuthor('Chloe')->setPublished(true);
        $review4 = (new Review())->setAuthor('Dora')->setPublished(true);
        $review5 = (new Review())->setAuthor('Eleanor')->setPublished(true);

        $review1View = $this->prophesize(View::class);
        $review2View = $this->prophesize(View::class);
        $review3View = $this->prophesize(View::class);
        $review4View = $this->prophesize(View::class);
        $review5View = $this->prophesize(View::class);

        $branch1 = (new Branch())->setName('Dereham')->addReview($review1)->addReview($review2)->addReview($review3);
        $branch2 = (new Branch())->setName('Swaffham')->addReview($review4)->addReview($review5);
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

        $output = $this->adminAgencyFactory->createHome($agency);

        $this->assertEquals($agencyModel->reveal(), $output->getAgency());
        $this->assertCount(3, $output->getBranches());
        $this->assertCount(5, $output->getReviews());
    }
}
