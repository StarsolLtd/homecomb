<?php

namespace App\Tests\Unit\Factory;

use App\Entity\City;
use App\Entity\District;
use App\Entity\Property;
use App\Entity\TenancyReview;
use App\Exception\DeveloperException;
use App\Factory\CityFactory;
use App\Factory\FlatModelFactory;
use App\Factory\PropertyFactory;
use App\Factory\TenancyReviewFactory;
use App\Model\City\City as CityModel;
use App\Model\District\Flat as FlatDistrict;
use App\Model\Property\VendorProperty;
use App\Model\TenancyReview\View;
use App\Service\CityService;
use App\Service\DistrictService;
use App\Util\PropertyHelper;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Factory\PropertyFactory
 */
final class PropertyFactoryTest extends TestCase
{
    use ProphecyTrait;

    private PropertyFactory $propertyFactory;

    private ObjectProphecy $cityService;
    private ObjectProphecy $districtService;
    private ObjectProphecy $propertyHelper;
    private ObjectProphecy $cityFactory;
    private ObjectProphecy $flatModelFactory;
    private ObjectProphecy $tenancyReviewFactory;

    public function setUp(): void
    {
        $this->cityService = $this->prophesize(CityService::class);
        $this->districtService = $this->prophesize(DistrictService::class);
        $this->propertyHelper = $this->prophesize(PropertyHelper::class);
        $this->cityFactory = $this->prophesize(CityFactory::class);
        $this->flatModelFactory = $this->prophesize(FlatModelFactory::class);
        $this->tenancyReviewFactory = $this->prophesize(TenancyReviewFactory::class);

        $this->propertyFactory = new PropertyFactory(
            $this->cityService->reveal(),
            $this->districtService->reveal(),
            $this->propertyHelper->reveal(),
            $this->cityFactory->reveal(),
            $this->flatModelFactory->reveal(),
            $this->tenancyReviewFactory->reveal(),
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
            'City of Cambridge',
            'Victoria Road',
            'England',
            'CB4 3LF',
            52.10101,
            -0.47261,
            true
        );
        $city = $this->prophesize(City::class);
        $district = $this->prophesize(District::class);

        $this->propertyHelper->generateSlug(Argument::type(Property::class))
            ->shouldBeCalledOnce()
            ->willReturn('ccc5382816c1');

        $this->cityService->findOrCreate('Cambridge', 'Cambridgeshire', 'UK')
            ->shouldBeCalledOnce()
            ->willReturn($city);

        $this->districtService->findOrCreate('City of Cambridge', 'Cambridgeshire', 'UK')
            ->shouldBeCalledOnce()
            ->willReturn($district);

        $property = $this->propertyFactory->createEntityFromVendorPropertyModel($vendorPropertyModel);

        $this->assertEquals(789, $property->getVendorPropertyId());
        $this->assertEquals('249 Victoria Road', $property->getAddressLine1());
        $this->assertEquals('', $property->getAddressLine2());
        $this->assertEquals('', $property->getAddressLine3());
        $this->assertEquals('', $property->getAddressLine4());
        $this->assertEquals('Arbury', $property->getLocality());
        $this->assertEquals('Cambridge', $property->getAddressCity());
        $this->assertEquals('City of Cambridge', $property->getAddressDistrict());
        $this->assertEquals('Cambridgeshire', $property->getCounty());
        $this->assertEquals('Victoria Road', $property->getThoroughfare());
        $this->assertEquals('CB4 3LF', $property->getPostcode());
        $this->assertEquals(52.10101, $property->getLatitude());
        $this->assertEquals(-0.47261, $property->getLongitude());
        $this->assertEquals($city->reveal(), $property->getCity());
        $this->assertEquals($district->reveal(), $property->getDistrict());
    }

    /**
     * @covers \App\Factory\PropertyFactory::createEntityFromVendorPropertyModel
     * Test throws exception if vendor property ID is null
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
        $review1 = (new TenancyReview())
            ->setAuthor('Jack Harper')
            ->setTitle('I was a tenant here')
            ->setContent('I liked the colour of the sink')
            ->setPublished(true)
        ;

        $review1View = $this->prophesize(View::class);

        $review2 = (new TenancyReview())
            ->setAuthor('Andrea Smith')
            ->setTitle('I stayed here 2 years')
            ->setContent('I liked the colour of the curtains')
            ->setPublished(true)
        ;

        $review2View = $this->prophesize(View::class);

        $city = (new City())
            ->setSlug('test')
            ->setName('Cambridge')
            ->setCounty('Cambridgeshire')
            ->setCountryCode('UK');

        $district = (new District())
            ->setSlug('test-district-slug')
            ->setName('City of Cambridge')
            ->setCounty('Cambridgeshire')
            ->setCountryCode('UK');

        $property = (new Property())
            ->setSlug('propertyslug')
            ->setAddressLine1('29 Bateman Street')
            ->setLocality(null)
            ->setAddressCity('Cambridge')
            ->setPostcode('CB3 6HC')
            ->setLatitude(52.19547)
            ->setLongitude(0.1283)
            ->setCity($city)
            ->setDistrict($district)
            ->addTenancyReview($review1)
            ->addTenancyReview($review2)
        ;

        $this->tenancyReviewFactory->createViewFromEntity($review1)
            ->shouldBeCalledOnce()
            ->willReturn($review1View)
        ;
        $this->tenancyReviewFactory->createViewFromEntity($review2)
            ->shouldBeCalledOnce()
            ->willReturn($review2View)
        ;

        $cityModel = $this->prophesize(CityModel::class);

        $this->cityFactory->createModelFromEntity($city)
            ->shouldBeCalledOnce()
            ->willReturn($cityModel);

        $districtFlatModel = $this->prophesize(FlatDistrict::class);

        $this->flatModelFactory->getDistrictFlatModel($district)
            ->shouldBeCalledOnce()
            ->willReturn($districtFlatModel);

        $view = $this->propertyFactory->createViewFromEntity($property);

        $this->assertEquals('propertyslug', $view->getSlug());
        $this->assertEquals('29 Bateman Street', $view->getAddressLine1());
        $this->assertNull($view->getLocality());
        $this->assertEquals('Cambridge', $view->getAddressCity());
        $this->assertEquals('CB3 6HC', $view->getPostcode());
        $this->assertEquals(52.19547, $view->getLatitude());
        $this->assertEquals(0.1283, $view->getLongitude());
        $this->assertCount(2, $view->getTenancyReviews());
        $this->assertEquals($cityModel->reveal(), $view->getCity());
        $this->assertEquals($districtFlatModel->reveal(), $view->getDistrict());
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
