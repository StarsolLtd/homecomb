<?php

namespace App\Service;

use App\Entity\Property;
use App\Model\LookupPropertyIdInput;
use App\Model\LookupPropertyIdOutput;
use App\Repository\PropertyRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class PropertyService
{
    private EntityManagerInterface $entityManager;
    private PropertyRepository $propertyRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        PropertyRepository $propertyRepository
    ) {
        $this->entityManager = $entityManager;
        $this->propertyRepository = $propertyRepository;
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
                ->setCreatedAt(new DateTime())
                ->setUpdatedAt(new DateTime());
            $this->entityManager->persist($property);
            $this->entityManager->flush();
        }

        return new LookupPropertyIdOutput($property->getId());
    }
}
