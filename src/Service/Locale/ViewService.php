<?php

namespace App\Service\Locale;

use App\Factory\LocaleFactory;
use App\Model\Locale\View;
use App\Repository\Locale\LocaleRepository;

class ViewService
{
    public function __construct(
        private LocaleFactory $localeFactory,
        private LocaleRepository $localeRepository,
    ) {
    }

    public function getViewBySlug(string $slug): View
    {
        $locale = $this->localeRepository->findOnePublishedBySlug($slug);

        return $this->localeFactory->createViewFromEntity($locale);
    }
}
