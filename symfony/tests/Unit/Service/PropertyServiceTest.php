<?php

namespace App\Tests\Unit\Service;

use App\Entity\Property;
use App\Factory\PropertyFactory;
use App\Model\Property\VendorProperty;
use App\Model\Property\View;
use App\Repository\PropertyRepository;
use App\Service\GetAddressService;
use App\Service\PropertyService;
use App\Tests\Unit\EntityManagerTrait;
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
     */
    public function testDeterminePropertySlugFromVendorPropertyIdWherePropertyAlreadyExists(): void
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
     */
    public function testDeterminePropertySlugFromVendorPropertyIdWherePropertyDoesNotExists(): void
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
}
