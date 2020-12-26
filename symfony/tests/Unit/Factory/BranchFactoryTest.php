<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Review;
use App\Factory\BranchFactory;
use App\Factory\ReviewFactory;
use App\Model\Branch\CreateBranchInput;
use App\Model\Review\View;
use App\Util\BranchHelper;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class BranchFactoryTest extends TestCase
{
    use ProphecyTrait;

    private BranchFactory $branchFactory;

    private $branchHelper;
    private $reviewFactory;

    public function setUp(): void
    {
        $this->branchHelper = $this->prophesize(BranchHelper::class);
        $this->reviewFactory = $this->prophesize(ReviewFactory::class);

        $this->branchFactory = new BranchFactory(
            $this->branchHelper->reveal(),
            $this->reviewFactory->reveal(),
        );
    }

    public function testCreateBranchEntityFromCreateBranchInputModel(): void
    {
        $createBranchInput = new CreateBranchInput(
            'Test Branch Name',
            '0700 100 200',
            null,
            'sample'
        );

        $agency = new Agency();

        $this->branchHelper->generateSlug(Argument::type(Branch::class))
            ->shouldBeCalledOnce()
            ->willReturn('ccc5382816c1');

        $branch = $this->branchFactory->createBranchEntityFromCreateBranchInputModel($createBranchInput, $agency);

        $this->assertEquals($agency, $branch->getAgency());
        $this->assertEquals('Test Branch Name', $branch->getName());
        $this->assertEquals('0700 100 200', $branch->getTelephone());
        $this->assertNull($branch->getEmail());
    }

    public function testCreateViewFromEntity(): void
    {
        $review1 = (new Review())
            ->setIdForTest(42)
            ->setAuthor('Jack Harper')
            ->setTitle('I was a tenant here')
            ->setContent('I liked the colour of the sink')
        ;

        $review1View = $this->prophesize(View::class);

        $review2 = (new Review())
            ->setIdForTest(43)
            ->setAuthor('Andrea Smith')
            ->setTitle('I stayed here 2 years')
            ->setContent('I liked the colour of the curtains')
        ;

        $review2View = $this->prophesize(View::class);

        $branch = (new Branch())
            ->setName('Test Name')
            ->setSlug('branchslug')
            ->setTelephone('0500 500 500')
            ->setEmail('test@branch.starsol.co.uk')
            ->addReview($review1)
            ->addReview($review2)
        ;

        $agency = (new Agency())
            ->setName('Test Agency')
            ->setSlug('agencyslug')
            ->addBranch($branch)
        ;

        $this->reviewFactory->createViewFromEntity($review1)
            ->shouldBeCalledOnce()
            ->willReturn($review1View)
        ;

        $this->reviewFactory->createViewFromEntity($review2)
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
