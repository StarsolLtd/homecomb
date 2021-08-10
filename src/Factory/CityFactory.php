<?php

namespace App\Factory;

use App\Entity\City;
use App\Factory\Review\LocaleReviewFactory;
use App\Model\City\City as CityModel;
use App\Util\CityHelper;

class CityFactory
{
    private CityHelper $cityHelper;
    private LocaleReviewFactory $localeReviewFactory;

    public function __construct(
        CityHelper $cityHelper,
        LocaleReviewFactory $localeReviewFactory,
    ) {
        $this->cityHelper = $cityHelper;
        $this->localeReviewFactory = $localeReviewFactory;
    }

    public function createEntity(string $name, ?string $county, string $countryCode): City
    {
        $city = (new City())
            ->setName($name)
            ->setCounty($county)
            ->setCountryCode($countryCode);

        $city->setSlug($this->cityHelper->generateSlug($city));

        return $city;
    }

    public function createModelFromEntity(City $entity): CityModel
    {
        $localeReviews = [];

        $locale = $entity->getLocale();
        if (null !== $locale) {
            foreach ($locale->getPublishedReviews() as $localeReviewEntity) {
                $localeReviews[] = $this->localeReviewFactory->createViewFromEntity($localeReviewEntity);
            }
        }

        return new CityModel(
            $entity->getSlug(),
            $entity->getName(),
            $entity->getCounty(),
            $entity->getCountryCode(),
            $localeReviews,
        );
    }
}
