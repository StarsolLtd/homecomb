<?php

namespace App\Service;

use App\Factory\PropertyFactory;
use App\Model\Property\View;
use App\Model\VendorProperty;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;

class PropertyService
{
    private EntityManagerInterface $entityManager;
    private PropertyFactory $propertyFactory;
    private PropertyRepository $propertyRepository;
    private GetAddressService $getAddressService;

    public function __construct(
        EntityManagerInterface $entityManager,
        PropertyFactory $propertyFactory,
        PropertyRepository $propertyRepository,
        GetAddressService $getAddressService
    ) {
        $this->entityManager = $entityManager;
        $this->propertyFactory = $propertyFactory;
        $this->propertyRepository = $propertyRepository;
        $this->getAddressService = $getAddressService;
    }

    public function determinePropertySlugFromVendorPropertyId(string $vendorPropertyId): ?string
    {
        $property = $this->propertyRepository->findOneBy(['vendorPropertyId' => $vendorPropertyId]);

        if (null !== $property) {
            return $property->getSlug();
        }

        $vendorProperty = $this->getAddressService->getAddress($vendorPropertyId);

        return $this->getPropertySlugByVendorProperty($vendorProperty);
    }

    public function getPropertySlugByVendorProperty(VendorProperty $vendorProperty): ?string
    {
        $property = $this->propertyRepository->findOneBy(
            [
                'vendorPropertyId' => $vendorProperty->getVendorPropertyId(),
            ]
        );

        if (null === $property) {
            $property = $this->propertyFactory->createEntityFromVendorPropertyModel($vendorProperty);
            $this->entityManager->persist($property);
            $this->entityManager->flush();
        }

        return $property->getSlug();
    }

    public function getViewBySlug(string $slug): View
    {
        $branch = $this->propertyRepository->findOnePublishedBySlug($slug);

        return $this->propertyFactory->createViewFromEntity($branch);
    }
}
