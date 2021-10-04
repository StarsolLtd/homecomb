<?php

namespace App\Service;

use App\Factory\LocaleFactory;
use App\Model\Locale\LocaleSearchResults;
use App\Repository\Locale\LocaleRepository;

class LocaleService
{
    public function __construct(
        private LocaleFactory $localeFactory,
        private LocaleRepository $localeRepository,
    ) {
    }

    public function search(string $query): LocaleSearchResults
    {
        $results = $this->localeRepository->findBySearchQuery($query);

        return $this->localeFactory->createLocaleSearchResults($query, $results);
    }
}
