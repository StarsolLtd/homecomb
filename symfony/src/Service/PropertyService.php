<?php

namespace App\Service;

use App\Entity\Property;
use App\Model\LookupPropertyIdInput;
use App\Model\LookupPropertyIdOutput;
use App\Model\PropertySuggestion;
use App\Model\SuggestPropertyInput;
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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function suggestProperty(SuggestPropertyInput $input): array
    {
        return $this->getAddressService->autocomplete($input->getSearch());
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
                ->setCountryCode($input->getCountryCode())
                ->setVendorPropertyId($input->getVendorPropertyId())
                ->setCreatedAt(new DateTime())
                ->setUpdatedAt(new DateTime());
            $this->entityManager->persist($property);
            $this->entityManager->flush();
        }

        return new LookupPropertyIdOutput($property->getId());
    }
}
