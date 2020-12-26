<?php

namespace App\Tests\Unit\Service;

use App\Entity\Property;
use App\Factory\PropertyFactory;
use App\Model\Property\View;
use App\Repository\PropertyRepository;
use App\Service\GetAddressService;
use App\Service\PropertyService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class PropertyServiceTest extends TestCase
{
    use ProphecyTrait;

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
}
