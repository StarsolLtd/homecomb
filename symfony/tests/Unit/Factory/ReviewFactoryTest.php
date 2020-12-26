<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Property;
use App\Entity\Review;
use App\Factory\AgencyFactory;
use App\Factory\BranchFactory;
use App\Factory\PropertyFactory;
use App\Factory\ReviewFactory;
use App\Model\Agency\Flat as FlatAgency;
use App\Model\Branch\Flat as FlatBranch;
use App\Model\Property\Flat as FlatProperty;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ReviewFactoryTest extends TestCase
{
    use ProphecyTrait;

    private ReviewFactory $reviewFactory;

    private $agencyFactory;
    private $branchFactory;
    private $propertyFactory;

    public function setUp(): void
    {
        $this->agencyFactory = $this->prophesize(AgencyFactory::class);
        $this->branchFactory = $this->prophesize(BranchFactory::class);
        $this->propertyFactory = $this->prophesize(PropertyFactory::class);

        $this->reviewFactory = new ReviewFactory(
            $this->agencyFactory->reveal(),
            $this->branchFactory->reveal(),
            $this->propertyFactory->reveal()
        );
    }

    public function testCreateViewFromEntity(): void
    {
        $branch = (new Branch());
        $agency = (new Agency())->addBranch($branch);
        $property = (new Property());

        $flatBranch = (new FlatBranch('branchslug', 'Test Branch Name'));
        $flatAgency = (new FlatAgency('agencyslug', 'Test Agency Name'));
        $flatProperty = (new FlatProperty('propertyslug', '123 Test Street', 'CB4 3LF'));

        $this->agencyFactory->createFlatModelFromEntity($agency)
            ->shouldBeCalledOnce()
            ->willReturn($flatAgency);
        $this->branchFactory->createFlatModelFromEntity($branch)
            ->shouldBeCalledOnce()
            ->willReturn($flatBranch);
        $this->propertyFactory->createFlatModelFromEntity($property)
            ->shouldBeCalledOnce()
            ->willReturn($flatProperty);

        $review = (new Review())
            ->setBranch($branch)
            ->setProperty($property)
            ->setIdForTest(789)
            ->setAuthor('Gina Gee')
            ->setTitle('Test Title')
            ->setContent('I lived here, it was nice.');

        $view = $this->reviewFactory->createViewFromEntity($review);

        $this->assertEquals('Test Branch Name', $view->getBranch()->getName());
        $this->assertEquals('Test Agency Name', $view->getAgency()->getName());
        $this->assertNull($view->getAgency()->getLogoImageFilename());
        $this->assertEquals('123 Test Street', $view->getProperty()->getAddressLine1());
        $this->assertEquals(789, $view->getId());
        $this->assertEquals('Gina Gee', $view->getAuthor());
        $this->assertEquals('Test Title', $view->getTitle());
        $this->assertEquals('I lived here, it was nice.', $view->getContent());
    }
}
