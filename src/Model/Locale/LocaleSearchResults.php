<?php

namespace App\Model\Locale;

class LocaleSearchResults
{
    /**
     * @param Flat[] $locales
     */
    public function __construct(
        private string $query,
        private array $locales
    ) {
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @return Flat[]
     */
    public function getLocales(): array
    {
        return $this->locales;
    }
}
