<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\TenancyReview;
use App\Factory\BranchFactory;
use App\Factory\TenancyReviewFactory;
use App\Model\Branch\CreateBranchInput;
use App\Model\TenancyReview\View;
use App\Util\BranchHelper;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Factory\BranchFactory
 */
class BranchFactoryTest extends TestCase
{
    use ProphecyTrait;

    private BranchFactory $branchFactory;

    private ObjectProphecy $branchHelper;
    private ObjectProphecy $tenancyReviewFactory;

    public function setUp(): void
    {
        $this->branchHelper = $this->prophesize(BranchHelper::class);
        $this->tenancyReviewFactory = $this->prophesize(TenancyReviewFactory::class);

        $this->branchFactory = new BranchFactory(
            $this->branchHelper->reveal(),
            $this->tenancyReviewFactory->reveal(),
        );
    }

    /**
     * @covers \App\Factory\BranchFactory::createEntityFromCreateBranchInput
     */
    public function testCreateEntityFromCreateBranchInput1(): void
    {
        $createBranchInput = new CreateBranchInput(
            'Test Branch Name',
            '0700 100 200',
            null,
            'sample'
        );

        $agency = $this->prophesize(Agency::class);

        $this->branchHelper->generateSlug(Argument::type(Branch::class))
            ->shouldBeCalledOnce()
            ->willReturn('ccc5382816c1');

        $branch = $this->branchFactory->createEntityFromCreateBranchInput($createBranchInput, $agency->reveal());

        $this->assertEquals($agency->reveal(), $branch->getAgency());
        $this->assertEquals('Test Branch Name', $branch->getName());
        $this->assertEquals('0700 100 200', $branch->getTelephone());
        $this->assertNull($branch->getEmail());
    }

    /**
     * @covers \App\Factory\BranchFactory::createViewFromEntity
     */
    public function testCreateViewFromEntity1(): void
    {
        $branch = (new Branch())
            ->setName('Test Name')
            ->setSlug('branchslug')
            ->setTelephone('0500 500 500')
            ->setEmail('test@branch.starsol.co.uk')
        ;

        $agency = (new Agency())
            ->setName('Test Agency')
            ->setSlug('agencyslug')
            ->addBranch($branch)
        ;

        $review1 = $this->prophesize(TenancyReview::class);
        $review1->isPublished()->shouldBeCalledOnce()->willReturn(true);
        $review1->setBranch($branch)->shouldBeCalledOnce()->willReturn($review1);
        $review1View = $this->prophesize(View::class);

        $review2 = $this->prophesize(TenancyReview::class);
        $review2->isPublished()->shouldBeCalledOnce()->willReturn(true);
        $review2->setBranch($branch)->shouldBeCalledOnce()->willReturn($review2);
        $review2View = $this->prophesize(View::class);

        $branch->addTenancyReview($review1->reveal())->addTenancyReview($review2->reveal());

        $this->tenancyReviewFactory->createViewFromEntity($review1)
            ->shouldBeCalledOnce()
            ->willReturn($review1View)
        ;

        $this->tenancyReviewFactory->createViewFromEntity($review2)
            ->shouldBeCalledOnce()
            ->willReturn($review2View)
        ;

        $view = $this->branchFactory->createViewFromEntity($branch);

        $this->assertEquals('Test Name', $view->getBranch()->getName());
        $this->assertEquals('branchslug', $view->getBranch()->getSlug());
        $this->assertEquals('0500 500 500', $view->getBranch()->getTelephone());
        $this->assertEquals('test@branch.starsol.co.uk', $view->getBranch()->getEmail());
        $this->assertEquals('Test Agency', $view->getAgency()->getName());
        $this->assertEquals('agencyslug', $view->getAgency()->getSlug());
        $this->assertNull($view->getAgency()->getLogoImageFilename());
    }
}
