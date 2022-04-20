<?php

namespace App\Tests\Unit\Service;

use App\Entity\Property;
use App\Factory\PropertyFactory;
use App\Model\Property\PropertySuggestion;
use App\Model\Property\VendorProperty;
use App\Model\Property\View;
use App\Repository\PropertyRepositoryInterface;
use App\Service\GetAddressService;
use App\Service\PropertyService;
use App\Tests\Unit\EntityManagerTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class PropertyServiceTest extends TestCase
{
    use ProphecyTrait;
    use EntityManagerTrait;

    private PropertyService $propertyService;

    private ObjectProphecy $propertyFactory;
    private ObjectProphecy $propertyRepository;
    private ObjectProphecy $getAddressService;

    public function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->propertyFactory = $this->prophesize(PropertyFactory::class);
        $this->propertyRepository = $this->prophesize(PropertyRepositoryInterface::class);
        $this->getAddressService = $this->prophesize(GetAddressService::class);

        $this->propertyService = new PropertyService(
            $this->entityManager->reveal(),
            $this->propertyFactory->reveal(),
            $this->propertyRepository->reveal(),
            $this->getAddressService->reveal(),
        );
    }

    public function testGetViewBySlug(): void
    {
        $property = $this->prophesize(Property::class);
        $view = $this->prophesize(View::class);

        $this->propertyRepository->findOnePublishedBySlug('property-slug')
            ->shouldBeCalledOnce()
            ->willReturn($property);

        $this->propertyFactory->createViewFromEntity($property)
            ->shouldBeCalledOnce()
            ->willReturn($view);

        $result = $this->propertyService->getViewBySlug('property-slug');

        $this->assertEquals($view->reveal(), $result);
    }

    /**
     * Test determinePropertySlugFromVendorPropertyId method where property already exists.
     */
    public function testDeterminePropertySlugFromVendorPropertyId1(): void
    {
        $property = $this->prophesize(Property::class);
        $property->getSlug()->shouldBeCalledOnce()->willReturn('property-slug');

        $this->propertyRepository->findOneByVendorPropertyIdOrNull('vendor-property-id')
            ->shouldBeCalledOnce()
            ->willReturn($property);

        $output = $this->propertyService->determinePropertySlugFromVendorPropertyId('vendor-property-id');

        $this->assertEquals('property-slug', $output);
        $this->assertEntityManagerUnused();
    }

    /**
     * Test determinePropertySlugFromVendorPropertyId method where property does not exist.
     */
    public function testDeterminePropertySlugFromVendorPropertyId2(): void
    {
        $property = $this->prophesize(Property::class);
        $vendorPropertyModel = $this->prophesize(VendorProperty::class);

        $this->propertyRepository->findOneByVendorPropertyIdOrNull('vendor-property-id')
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $this->getAddressService->getAddress('vendor-property-id')
            ->shouldBeCalled()
            ->willReturn($vendorPropertyModel);

        $this->propertyFactory->createEntityFromVendorPropertyModel($vendorPropertyModel)
            ->shouldBeCalled()
            ->willReturn($property);

        $property->getSlug()
            ->shouldBeCalled()
            ->willReturn('property-slug');

        $output = $this->propertyService->determinePropertySlugFromVendorPropertyId('vendor-property-id');

        $this->assertEquals('property-slug', $output);
        $this->assertEntitiesArePersistedAndFlush([$property]);
    }

    /**
     * Test determinePropertySlugFromAddress method where property already exists.
     */
    public function testDeterminePropertySlugFromAddress1(): void
    {
        $property = $this->prophesize(Property::class);

        $this->propertyRepository->findOneByAddressOrNull('181 Victoria Road', 'CB4 3LF')
            ->shouldBeCalledOnce()
            ->willReturn($property);

        $property->getSlug()
            ->shouldBeCalledOnce()
            ->willReturn('test-slug');

        $output = $this->propertyService->determinePropertySlugFromAddress('181 Victoria Road', 'CB4 3LF');

        $this->assertEquals('test-slug', $output);
        $this->assertEntityManagerUnused();
    }

    /**
     * Test determinePropertySlugFromAddress where there is no an existing property, and no suggestions would be found
     * via the API.
     */
    public function testDeterminePropertySlugFromAddress2(): void
    {
        $this->propertyRepository->findOneByAddressOrNull('10101 Nowhere Lane', 'NR99 9ZZ')
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $this->getAddressService->autocomplete('10101 Nowhere Lane, NR99 9ZZ')
            ->shouldBeCalledOnce()
            ->willReturn([]);

        $output = $this->propertyService->determinePropertySlugFromAddress('10101 Nowhere Lane', 'NR99 9ZZ');

        $this->assertNull($output);
    }

    /**
     * Test determinePropertySlugFromAddress where there is no an existing property, and one is successfully
     * suggested by the API.
     */
    public function testDeterminePropertySlugFromAddress3(): void
    {
        $property = $this->prophesize(Property::class);
        $suggestion = $this->prophesize(PropertySuggestion::class);
        $vendorProperty = $this->prophesize(VendorProperty::class);

        $this->propertyRepository->findOneByAddressOrNull('10101 Nowhere Lane', 'NR99 9ZZ')
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $this->getAddressService->autocomplete('10101 Nowhere Lane, NR99 9ZZ')
            ->shouldBeCalledOnce()
            ->willReturn([$suggestion]);

        $suggestion->getVendorId()->shouldBeCalledOnce()->willReturn('test-vendor-property-id');

        $this->getAddressService->getAddress('test-vendor-property-id')
            ->shouldBeCalledOnce()
            ->willReturn($vendorProperty);

        $this->propertyFactory->createEntityFromVendorPropertyModel($vendorProperty)
            ->shouldBeCalledOnce()
            ->willReturn($property);

        $this->assertEntitiesArePersistedAndFlush([$property]);

        $property->getSlug()->shouldBeCalledOnce()->willReturn('new-property-slug');

        $output = $this->propertyService->determinePropertySlugFromAddress('10101 Nowhere Lane', 'NR99 9ZZ');

        $this->assertEquals('new-property-slug', $output);
    }
}
