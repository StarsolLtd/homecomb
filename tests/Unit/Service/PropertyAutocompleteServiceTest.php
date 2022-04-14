<?php

namespace App\Tests\Unit\Service;

use App\Entity\Property;
use App\Model\Property\PropertySuggestion;
use App\Repository\PropertyRepositoryInterface;
use App\Service\GetAddressService;
use App\Service\PropertyAutocompleteService;
use App\Tests\Unit\EntityManagerTrait;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Service\PropertyAutocompleteService
 */
final class PropertyAutocompleteServiceTest extends TestCase
{
    use ProphecyTrait;
    use EntityManagerTrait;

    private PropertyAutocompleteService $propertyAutocompleteService;

    private ObjectProphecy $propertyRepository;
    private ObjectProphecy $getAddressService;

    public function setUp(): void
    {
        $this->propertyRepository = $this->prophesize(PropertyRepositoryInterface::class);
        $this->getAddressService = $this->prophesize(GetAddressService::class);

        $this->propertyAutocompleteService = new PropertyAutocompleteService(
            $this->propertyRepository->reveal(),
            $this->getAddressService->reveal(),
        );
    }

    /**
     * Test that
     * - An amalgamation of results from GetAddressService and app database are returned.
     * - Results from the app database should be skipped, if they were already found via GetAddressService.
     * - Results are sorted so GetAddressService sourced results that already exist in app database appear first.
     *
     * @covers \App\Service\PropertyAutocompleteService::search
     */
    public function testSearch1()
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

        $output = $this->propertyAutocompleteService->search('43 Du');

        $this->assertCount(4, $output);

        $this->assertEquals('test-vendor-id-2', $output[0]->getVendorId());
        $this->assertEquals('test-vendor-id-1', $output[1]->getVendorId());
        $this->assertEquals('test-slug-1', $output[2]->getPropertySlug());
        $this->assertEquals('test-slug-2', $output[3]->getPropertySlug());
    }
}
