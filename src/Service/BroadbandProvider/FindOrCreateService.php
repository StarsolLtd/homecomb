<?php

namespace App\Service\BroadbandProvider;

use App\Entity\BroadbandProvider;
use App\Factory\BroadbandProviderFactory;
use App\Repository\BroadbandProviderRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class FindOrCreateService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private BroadbandProviderFactory $broadbandProviderFactory,
        private BroadbandProviderRepositoryInterface $broadbandProviderRepository
    ) {
    }

    public function findOrCreate(string $name, ?string $countryCode): BroadbandProvider
    {
        $name = trim($name);
        if (null !== $countryCode) {
            $countryCode = trim($countryCode);
        }

        $broadbandProvider = $this->broadbandProviderRepository->findOneBy(
            [
                'name' => $name,
                'countryCode' => $countryCode,
            ]
        );
        if (null !== $broadbandProvider) {
            return $broadbandProvider;
        }

        $broadbandProvider = $this->broadbandProviderFactory->createEntityFromNameAndCountryCode($name, $countryCode);

        $this->entityManager->persist($broadbandProvider);
        $this->entityManager->flush();

        return $broadbandProvider;
    }
}
