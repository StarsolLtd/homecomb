<?php

namespace App\Service;

use App\Exception\DeveloperException;
use App\Exception\FailureException;
use App\Factory\PropertyFactory;
use App\Model\Property\View;
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

    /**
     * @throws DeveloperException
     * @throws FailureException
     */
    public function determinePropertySlugFromVendorPropertyId(string $vendorPropertyId): ?string
    {
        $property = $this->propertyRepository->findOneByVendorPropertyIdOrNull($vendorPropertyId);

        if (null !== $property) {
            return $property->getSlug();
        }

        $vendorProperty = $this->getAddressService->getAddress($vendorPropertyId);

        $property = $this->propertyFactory->createEntityFromVendorPropertyModel($vendorProperty);

        $this->entityManager->persist($property);
        $this->entityManager->flush();

        return $property->getSlug();
    }

    public function determinePropertySlugFromAddress(string $addressLine1, string $postcode): ?string
    {
        $property = $this->propertyRepository->findOneByAddressOrNull($addressLine1, $postcode);
        if (null !== $property) {
            return $property->getSlug();
        }

        $suggestions = $this->getAddressService->autocomplete($addressLine1.', '.$postcode);
        $vendorPropertyId = empty($suggestions) ? null : $suggestions[0]->getVendorId();
        if (null === $vendorPropertyId) {
            return null;
        }

        $vendorProperty = $this->getAddressService->getAddress($vendorPropertyId);

        $property = $this->propertyFactory->createEntityFromVendorPropertyModel($vendorProperty);

        $this->entityManager->persist($property);
        $this->entityManager->flush();

        return $property->getSlug();
    }

    public function getViewBySlug(string $slug): View
    {
        $branch = $this->propertyRepository->findOnePublishedBySlug($slug);

        return $this->propertyFactory->createViewFromEntity($branch);
    }
}
