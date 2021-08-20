<?php

namespace App\Model\Locale;

class LocaleSearchResults
{
    private string $query;
    private array $locales;

    /**
     * @param Flat[] $locales
     */
    public function __construct(
        string $query,
        array $locales
    ) {
        $this->query = $query;
        $this->locales = $locales;
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
