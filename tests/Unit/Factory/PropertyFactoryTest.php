<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Property;
use App\Entity\Review;
use App\Exception\DeveloperException;
use App\Factory\PropertyFactory;
use App\Factory\ReviewFactory;
use App\Model\Property\VendorProperty;
use App\Model\Review\View;
use App\Util\PropertyHelper;
use function file_get_contents;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Factory\PropertyFactory
 */
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

    /**
     * @covers \App\Factory\PropertyFactory::createEntityFromVendorPropertyModel
     */
    public function testCreatePropertyEntityFromVendorPropertyModel1(): void
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

    /**
     * @covers \App\Factory\PropertyFactory::createEntityFromVendorPropertyModel
     * Test throws exception if vendor vendor property ID is null
     */
    public function testCreatePropertyEntityFromVendorPropertyModel2(): void
    {
        $vendorPropertyModel = $this->prophesize(VendorProperty::class);

        $vendorPropertyModel->getVendorPropertyId()->shouldBeCalledOnce()->willReturn(null);

        $this->expectException(DeveloperException::class);
        $this->expectExceptionMessage('Unable to create a property entity without a vendor property ID.');

        $this->propertyFactory->createEntityFromVendorPropertyModel($vendorPropertyModel->reveal());
    }

    /**
     * @covers \App\Factory\PropertyFactory::createViewFromEntity
     */
    public function testCreateViewFromEntity1(): void
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
            ->setLatitude(52.19547)
            ->setLongitude(0.1283)
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
        $this->assertEquals(52.19547, $view->getLatitude());
        $this->assertEquals(0.1283, $view->getLongitude());
        $this->assertCount(2, $view->getReviews());
    }

    /**
     * @covers \App\Factory\PropertyFactory::createPostcodePropertiesFromFindResponseContent
     */
    public function testCreatePostcodePropertiesFromFindResponseContent1(): void
    {
        $content = file_get_contents(__DIR__.'/files/getAddress_find_expand_response.json');

        $output = $this->propertyFactory->createPostcodePropertiesFromFindResponseContent($content);

        $this->assertEquals('NN1 3ER', $output->getPostcode());
        $this->assertCount(70, $output->getVendorProperties());
        $this->assertContainsOnlyInstancesOf(VendorProperty::class, $output->getVendorProperties());
    }
}
