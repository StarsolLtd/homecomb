<?php

namespace App\Tests\Unit\Service;

use App\Entity\Property;
use App\Factory\PropertyFactory;
use App\Model\Property\PropertySuggestion;
use App\Model\Property\VendorProperty;
use App\Model\Property\View;
use App\Repository\PropertyRepository;
use App\Service\GetAddressService;
use App\Service\PropertyService;
use App\Tests\Unit\EntityManagerTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Service\PropertyService
 */
class PropertyServiceTest extends TestCase
{
    use ProphecyTrait;
    use EntityManagerTrait;

    private PropertyService $propertyService;

    private $entityManager;
    private $propertyFactory;
    private $propertyRepository;
    private $getAddressService;

    public function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->propertyFactory = $this->prophesize(PropertyFactory::class);
        $this->propertyRepository = $this->prophesize(PropertyRepository::class);
        $this->getAddressService = $this->prophesize(GetAddressService::class);

        $this->propertyService = new PropertyService(
            $this->entityManager->reveal(),
            $this->propertyFactory->reveal(),
            $this->propertyRepository->reveal(),
            $this->getAddressService->reveal(),
        );
    }

    /**
     * @covers \App\Service\PropertyService::getViewBySlug
     */
    public function testGetViewBySlug(): void
    {
        $property = (new Property());
        $view = new View(
            'propertyslug',
            '33 Bateman Street',
            'CB4 5TW',
            []
        );

        $this->propertyRepository->findOnePublishedBySlug('propertyslug')
            ->shouldBeCalledOnce()
            ->willReturn($property);

        $this->propertyFactory->createViewFromEntity($property)
            ->shouldBeCalledOnce()
            ->willReturn($view);

        $view = $this->propertyService->getViewBySlug('propertyslug');

        $this->assertEquals('propertyslug', $view->getSlug());
        $this->assertCount(0, $view->getReviews());
    }

    /**
     * @covers \App\Service\PropertyService::determinePropertySlugFromVendorPropertyId
     * Test where property already exists
     */
    public function testDeterminePropertySlugFromVendorPropertyId1(): void
    {
        $property = (new Property())->setSlug('propertyslug');

        $this->propertyRepository->findOneByVendorPropertyIdOrNull('vendorpropertyid')
            ->shouldBeCalledOnce()
            ->willReturn($property);

        $output = $this->propertyService->determinePropertySlugFromVendorPropertyId('vendorpropertyid');

        $this->assertEquals('propertyslug', $output);
        $this->assertEntityManagerUnused();
    }

    /**
     * @covers \App\Service\PropertyService::determinePropertySlugFromVendorPropertyId
     * Test where property does not exist
     */
    public function testDeterminePropertySlugFromVendorPropertyId2(): void
    {
        $property = $this->prophesize(Property::class);
        $vendorPropertyModel = $this->prophesize(VendorProperty::class);

        $this->propertyRepository->findOneByVendorPropertyIdOrNull('vendorpropertyid')
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $this->getAddressService->getAddress('vendorpropertyid')
            ->shouldBeCalled()
            ->willReturn($vendorPropertyModel);

        $this->propertyFactory->createEntityFromVendorPropertyModel($vendorPropertyModel)
            ->shouldBeCalled()
            ->willReturn($property);

        $property->getSlug()
            ->shouldBeCalled()
            ->willReturn('propertyslug');

        $output = $this->propertyService->determinePropertySlugFromVendorPropertyId('vendorpropertyid');

        $this->assertEquals('propertyslug', $output);
        $this->assertEntitiesArePersistedAndFlush([$property]);
    }

    /**
     * @covers \App\Service\PropertyService::determinePropertySlugFromAddress
     * Test where property already exists
     */
    public function testDeterminePropertySlugFromAddress1(): void
    {
        $property = $this->prophesize(Property::class);

        $this->propertyRepository->findOneByAddressOrNull('181 Victoria Road', 'CB4 3LF')
            ->shouldBeCalledOnce()
            ->willReturn($property);

        $property->getSlug()
            ->shouldBeCalledOnce()
            ->willReturn('testslug');

        $output = $this->propertyService->determinePropertySlugFromAddress('181 Victoria Road', 'CB4 3LF');

        $this->assertEquals('testslug', $output);
        $this->assertEntityManagerUnused();
    }

    /**
     * @covers \App\Service\PropertyService::determinePropertySlugFromAddress
     * Test where there is no an existing property, and no suggestions would be found via the API
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
     * @covers \App\Service\PropertyService::determinePropertySlugFromAddress
     * Test where there is no an existing property, and one is successfully suggested by the API
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

        $suggestion->getVendorId()->shouldBeCalledOnce()->willReturn('testvendorpropertyid');

        $this->getAddressService->getAddress('testvendorpropertyid')
            ->shouldBeCalledOnce()
            ->willReturn($vendorProperty);

        $this->propertyFactory->createEntityFromVendorPropertyModel($vendorProperty)
            ->shouldBeCalledOnce()
            ->willReturn($property);

        $this->assertEntitiesArePersistedAndFlush([$property]);

        $property->getSlug()->shouldBeCalledOnce()->willReturn('newpropertyslug');

        $output = $this->propertyService->determinePropertySlugFromAddress('10101 Nowhere Lane', 'NR99 9ZZ');

        $this->assertEquals('newpropertyslug', $output);
    }

    /**
     * @covers \App\Service\PropertyService::autocompleteSearch
     */
    public function testAutocompleteSearch1()
    {
        $suggestions = [
            new PropertySuggestion('43 Duckula Lane, Whitby, Yorkshire', 'test-vendor-id-1'),
            new PropertySuggestion('43 Duopoly Square, Norwich, Norfolk', 'test-vendor-id-2'),
        ];

        $property1 = $this->prophesize(Property::class);
        $property1->getAddressLine1()->shouldBeCalledOnce()->willReturn("43 Duke's Yard");
        $property1->getPostcode()->shouldBeCalledOnce()->willReturn('PE31 8RW');
        $property1->getVendorPropertyId()->shouldBeCalledOnce()->willReturn(null);
        $property1->getSlug()->shouldBeCalledOnce()->willReturn('test-slug-1');

        $property2 = $this->prophesize(Property::class);
        $property2->getAddressLine1()->shouldBeCalledOnce()->willReturn('43 Dune Buggy Lane');
        $property2->getPostcode()->shouldBeCalledOnce()->willReturn('CB1 1ZP');
        $property2->getVendorPropertyId()->shouldBeCalledOnce()->willReturn(null);
        $property2->getSlug()->shouldBeCalledOnce()->willReturn('test-slug-2');

        $property3 = $this->prophesize(Property::class);
        // Should be skipped as test-vendor-id-2 already in suggestions
        $property3->getVendorPropertyId()->shouldBeCalledOnce()->willReturn('test-vendor-id-2');

        $properties = (new ArrayCollection());
        $properties->add($property1->reveal());
        $properties->add($property2->reveal());
        $properties->add($property3->reveal());

        $this->getAddressService->autocomplete('43 Du')->shouldBeCalledOnce()->willReturn($suggestions);

        $this->propertyRepository->findBySearchQuery('43 Du', 3)->shouldBeCalledOnce()->willReturn($properties);

        $output = $this->propertyService->autocompleteSearch('43 Du');

        $this->assertCount(4, $output);

        $this->assertEquals('test-vendor-id-1', $output[0]->getVendorId());
        $this->assertEquals('test-vendor-id-2', $output[1]->getVendorId());
        $this->assertEquals('test-slug-1', $output[2]->getPropertySlug());
        $this->assertEquals('test-slug-2', $output[3]->getPropertySlug());
    }
}
