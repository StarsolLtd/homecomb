<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Property;
use App\Entity\Review;
use App\Factory\PropertyFactory;
use App\Factory\ReviewFactory;
use App\Model\Review\View;
use App\Model\VendorProperty;
use App\Util\PropertyHelper;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class PropertyFactoryTest extends TestCase
{
    use ProphecyTrait;

    private PropertyFactory $propertyFactory;

    private $propertyHelper;
    private $reviewFactory;

    public function setUp(): void
    {
        $this->propertyHelper = $this->prophesize(PropertyHelper::class);
        $this->reviewFactory = $this->prophesize(ReviewFactory::class);

        $this->propertyFactory = new PropertyFactory(
            $this->propertyHelper->reveal(),
            $this->reviewFactory->reveal(),
        );
    }

    public function testCreatePropertyEntityFromVendorPropertyModel(): void
    {
        $vendorPropertyModel = new VendorProperty(
            789,
            '249 Victoria Road',
            '',
            '',
            '',
            'Arbury',
            'Cambridge',
            'Cambridgeshire',
            'Cambridge',
            'England',
            'CB4 3LF',
            52.10101,
            -0.47261,
            true
        );

        $this->propertyHelper->generateSlug(Argument::type(Property::class))
            ->shouldBeCalledOnce()
            ->willReturn('ccc5382816c1');

        $property = $this->propertyFactory->createEntityFromVendorPropertyModel($vendorPropertyModel);

        $this->assertEquals(789, $property->getVendorPropertyId());
        $this->assertEquals('249 Victoria Road', $property->getAddressLine1());
        $this->assertEquals('', $property->getAddressLine2());
        $this->assertEquals('', $property->getAddressLine3());
        $this->assertEquals('', $property->getAddressLine4());
        $this->assertEquals('Arbury', $property->getLocality());
        $this->assertEquals('Cambridge', $property->getCity());
        $this->assertEquals('Cambridgeshire', $property->getCounty());
        $this->assertEquals('CB4 3LF', $property->getPostcode());
        $this->assertEquals(52.10101, $property->getLatitude());
        $this->assertEquals(-0.47261, $property->getLongitude());
    }

    public function testCreateViewFromEntity(): void
    {
        $review1 = (new Review())
            ->setAuthor('Jack Harper')
            ->setTitle('I was a tenant here')
            ->setContent('I liked the colour of the sink')
            ->setPublished(true)
        ;

        $review1View = $this->prophesize(View::class);

        $review2 = (new Review())
            ->setAuthor('Andrea Smith')
            ->setTitle('I stayed here 2 years')
            ->setContent('I liked the colour of the curtains')
            ->setPublished(true)
        ;

        $review2View = $this->prophesize(View::class);

        $property = (new Property())
            ->setSlug('propertyslug')
            ->setAddressLine1('29 Bateman Street')
            ->setPostcode('CB3 6HC')
            ->addReview($review1)
            ->addReview($review2)
        ;

        $this->reviewFactory->createViewFromEntity($review1)
            ->shouldBeCalledOnce()
            ->willReturn($review1View)
        ;
        $this->reviewFactory->createViewFromEntity($review2)
            ->shouldBeCalledOnce()
            ->willReturn($review2View)
        ;

        $view = $this->propertyFactory->createViewFromEntity($property);

        $this->assertEquals('propertyslug', $view->getSlug());
        $this->assertEquals('29 Bateman Street', $view->getAddressLine1());
        $this->assertEquals('CB3 6HC', $view->getPostcode());
        $this->assertCount(2, $view->getReviews());
    }
}
