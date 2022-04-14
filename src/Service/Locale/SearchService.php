<?php

namespace App\Service\Locale;

use App\Factory\LocaleFactory;
use App\Model\Locale\LocaleSearchResults;
use App\Repository\Locale\LocaleRepositoryInterface;

class SearchService
{
    public function __construct(
        private LocaleFactory $localeFactory,
        private LocaleRepositoryInterface $localeRepository,
    ) {
    }

    public function search(string $query): LocaleSearchResults
    {
        $results = $this->localeRepository->findBySearchQuery($query);

        return $this->localeFactory->createLocaleSearchResults($query, $results);
    }
}
