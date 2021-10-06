<?php

namespace App\Factory;

use App\Entity\BroadbandProvider;
use App\Util\BroadbandProviderHelper;

class BroadbandProviderFactory
{
    public function __construct(
        private BroadbandProviderHelper $broadbandProviderHelper,
    ) {
    }

    public function createEntityFromNameAndCountryCode(string $name, ?string $countryCode): BroadbandProvider
    {
        $broadbandProvider = (new BroadbandProvider())
            ->setName($name)
            ->setCountryCode($countryCode)
        ;

        $broadbandProvider->setSlug($this->broadbandProviderHelper->generateSlug($broadbandProvider));

        return $broadbandProvider;
    }
}
