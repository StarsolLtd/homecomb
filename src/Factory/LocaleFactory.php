<?php

namespace App\Factory;

use App\Entity\City;
use App\Entity\District;
use App\Entity\Locale\CityLocale;
use App\Entity\Locale\DistrictLocale;
use App\Entity\Locale\Locale;
use App\Entity\TenancyReview;
use App\Exception\DeveloperException;
use App\Factory\Review\LocaleReviewFactory;
use App\Model\Agency\ReviewsSummary;
use App\Model\Locale\AgencyReviewsSummary;
use App\Model\Locale\LocaleSearchResults;
use App\Model\Locale\View;
use App\Util\LocaleHelper;
use Doctrine\Common\Collections\ArrayCollection;

class LocaleFactory
{
    private LocaleHelper $localeHelper;
    private FlatModelFactory $flatModelFactory;
    private LocaleReviewFactory $localeReviewFactory;
    private TenancyReviewFactory $tenancyReviewFactory;

    public function __construct(
        LocaleHelper $localeHelper,
        FlatModelFactory $flatModelFactory,
        LocaleReviewFactory $localeReviewFactory,
        TenancyReviewFactory $tenancyReviewFactory
    ) {
        $this->localeHelper = $localeHelper;
        $this->flatModelFactory = $flatModelFactory;
        $this->localeReviewFactory = $localeReviewFactory;
        $this->tenancyReviewFactory = $tenancyReviewFactory;
    }

    public function createViewFromEntity(Locale $entity): View
    {
        $tenancyReviews = [];
        foreach ($entity->getPublishedTenancyReviews() as $tenancyReviewEntity) {
            $tenancyReviews[] = $this->tenancyReviewFactory->createViewFromEntity($tenancyReviewEntity);
        }

        $localeReviews = [];
        foreach ($entity->getPublishedReviews() as $localeReviewEntity) {
            $localeReviews[] = $this->localeReviewFactory->createViewFromEntity($localeReviewEntity);
        }

        $agencyReviewsSummary = $this->getAgencyReviewsSummary($entity);

        return new View(
            $entity->getSlug(),
            $entity->getName(),
            $entity->getContent(),
            $localeReviews,
            $tenancyReviews,
            $agencyReviewsSummary
        );
    }

    public function createCityLocaleEntity(City $city): CityLocale
    {
        $cityLocale = (new CityLocale())
            ->setCity($city)
            ->setName($city->getName())
            ->setPublished(true);

        $cityLocale->setSlug($this->localeHelper->generateSlug($cityLocale));

        assert($cityLocale instanceof CityLocale);

        return $cityLocale;
    }

    public function createDistrictLocaleEntity(District $district): DistrictLocale
    {
        $districtLocale = (new DistrictLocale())
            ->setDistrict($district)
            ->setName($district->getName())
            ->setPublished(true);

        $districtLocale->setSlug($this->localeHelper->generateSlug($districtLocale));

        assert($districtLocale instanceof DistrictLocale);

        return $districtLocale;
    }

    public function createLocaleSearchResults(string $query, ArrayCollection $results): LocaleSearchResults
    {
        $locales = [];
        foreach ($results as $result) {
            $locales[] = $this->flatModelFactory->getLocaleFlatModel($result);
        }

        return new LocaleSearchResults(
            $query,
            $locales
        );
    }

    public function getAgencyReviewsSummary(Locale $locale): AgencyReviewsSummary
    {
        $agencies = [];
        /** @var TenancyReview $tenancyReview */
        foreach ($locale->getPublishedTenancyReviewsWithPublishedAgency() as $tenancyReview) {
            $agency = $tenancyReview->getAgency();
            if (null === $agency) {
                throw new DeveloperException(sprintf('Review %s has no agency. ', $tenancyReview->getId()));
            }

            $slug = $agency->getSlug();
            if (!isset($agencies[$slug])) {
                $logoImage = $agency->getLogoImage();
                $logoImageFilename = $logoImage ? $logoImage->getImage() : null;
                $agencies[$slug] = [
                    'name' => $agency->getName(),
                    'logoImageFilename' => $logoImageFilename,
                    '5' => 0,
                    '4' => 0,
                    '3' => 0,
                    '2' => 0,
                    '1' => 0,
                    'score' => 0,
                    'totalRated' => 0,
                    'totalUnrated' => 0,
                ];
            }
            if (null !== $tenancyReview->getAgencyStars()) {
                $rating = $tenancyReview->getAgencyStars();
                ++$agencies[$slug][(string) $rating];
                ++$agencies[$slug]['totalRated'];
                $agencies[$slug]['score'] += $rating;
            } else {
                ++$agencies[$slug]['totalUnrated'];
            }
        }

        $agencyReviewSummaries = [];
        $reviewsCount = 0;
        foreach ($agencies as $agencySlug => $agency) {
            $reviewsCount += $agency['totalRated'];
            $meanRating = 0 < $agency['totalRated']
                ? round($agency['score'] / $agency['totalRated'], 2)
                : 0;

            $agencyReviewSummaries[] = new ReviewsSummary(
                $agencySlug,
                $agency['name'],
                $agency['logoImageFilename'],
                $agency['5'],
                $agency['4'],
                $agency['3'],
                $agency['2'],
                $agency['1'],
                $agency['totalRated'],
                $agency['totalUnrated'],
                $meanRating
            );
        }

        // Order agencies by mean rating, descending
        usort($agencyReviewSummaries, fn ($a, $b) => strcmp($b->getMeanRating(), $a->getMeanRating()));

        return new AgencyReviewsSummary($agencyReviewSummaries, $reviewsCount, count($agencies));
    }
}
