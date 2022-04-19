<?php

namespace App\Service;

use App\Exception\DeveloperException;
use App\Exception\FailureException;
use App\Factory\PropertyFactory;
use App\Model\Property\View;
use App\Repository\PropertyRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class PropertyService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PropertyFactory $propertyFactory,
        private PropertyRepositoryInterface $propertyRepository,
        private GetAddressService $getAddressService,
    ) {
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
        $property = $this->propertyRepository->findOnePublishedBySlug($slug);

        return $this->propertyFactory->createViewFromEntity($property);
    }
}
