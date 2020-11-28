<?php

namespace App\Service;

use App\Entity\Property;
use App\Factory\PropertyFactory;
use App\Model\LookupPropertyIdInput;
use App\Model\LookupPropertyIdOutput;
use App\Model\PropertySuggestion;
use App\Model\SuggestPropertyInput;
use App\Model\VendorProperty;
use App\Repository\PropertyRepository;
use DateTime;
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
     * @return PropertySuggestion[]
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function suggestProperty(SuggestPropertyInput $input): array
    {
        return $this->getAddressService->autocomplete($input->getSearch());
    }

    public function determinePropertyIdFromVendorPropertyId(string $vendorPropertyId): int
    {
        $vendorProperty = $this->getAddressService->getAddress($vendorPropertyId);

        return $this->getPropertyIdByVendorProperty($vendorProperty);
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

    public function lookupPropertyId(LookupPropertyIdInput $input): LookupPropertyIdOutput
    {
        $property = $this->propertyRepository->findOneBy(
            [
                'addressLine1' => $input->getAddressLine1(),
                'postcode' => $input->getPostcode(),
                'countryCode' => $input->getCountryCode(),
            ]
        );

        if (null === $property) {
            $property = (new Property())
                ->setAddressLine1($input->getAddressLine1())
                ->setPostcode($input->getPostcode())
                ->setCountryCode($input->getCountryCode())
                ->setVendorPropertyId($input->getVendorPropertyId())
                ->setCreatedAt(new DateTime())
                ->setUpdatedAt(new DateTime());
            $this->entityManager->persist($property);
            $this->entityManager->flush();
        }

        return new LookupPropertyIdOutput($property->getId());
    }

    public function getPropertyIdByVendorProperty(VendorProperty $vendorProperty): int
    {
        $property = $this->propertyRepository->findOneBy(
            [
                'vendorPropertyId' => $vendorProperty->getVendorPropertyId(),
            ]
        );

        if (null === $property) {
            $property = $this->propertyFactory->createPropertyEntityFromVendorPropertyModel($vendorProperty);
            $this->entityManager->persist($property);
            $this->entityManager->flush();
        }

        return $property->getId();
    }

    public function getPropertySlugByVendorProperty(VendorProperty $vendorProperty): ?string
    {
        $property = $this->propertyRepository->findOneBy(
            [
                'vendorPropertyId' => $vendorProperty->getVendorPropertyId(),
            ]
        );

        if (null === $property) {
            $property = $this->propertyFactory->createPropertyEntityFromVendorPropertyModel($vendorProperty);
            $this->entityManager->persist($property);
            $this->entityManager->flush();
        }

        return $property->getSlug();
    }
}
