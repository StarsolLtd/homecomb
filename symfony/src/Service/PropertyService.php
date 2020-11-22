<?php

namespace App\Service;

use App\Entity\Property;
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
    private PropertyRepository $propertyRepository;
    private GetAddressService $getAddressService;

    public function __construct(
        EntityManagerInterface $entityManager,
        PropertyRepository $propertyRepository,
        GetAddressService $getAddressService
    ) {
        $this->entityManager = $entityManager;
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

        return $this->getFetchPropertyIdByVendorProperty($vendorProperty);
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

    public function getFetchPropertyIdByVendorProperty(VendorProperty $vendorProperty): int
    {
        $property = $this->propertyRepository->findOneBy(
            [
                'vendorPropertyId' => $vendorProperty->getVendorPropertyId(),
            ]
        );

        if (null === $property) {
            $property = (new Property())
                ->setAddressLine1($vendorProperty->getAddressLine1())
                ->setAddressLine2($vendorProperty->getAddressLine2())
                ->setAddressLine3($vendorProperty->getAddressLine3())
                ->setCity($vendorProperty->getCity())
                ->setPostcode($vendorProperty->getPostcode())
                ->setCountryCode('UK')
                ->setVendorPropertyId($vendorProperty->getVendorPropertyId())
                ->setCreatedAt(new DateTime())
                ->setUpdatedAt(new DateTime());
            $this->entityManager->persist($property);
            $this->entityManager->flush();
        }

        return $property->getId();
    }
}
