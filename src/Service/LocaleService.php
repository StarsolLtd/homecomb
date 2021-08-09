<?php

namespace App\Service;

use App\Entity\City;
use App\Entity\District;
use App\Entity\Locale\CityLocale;
use App\Entity\Locale\DistrictLocale;
use App\Entity\Locale\Locale;
use App\Entity\TenancyReview;
use App\Factory\LocaleFactory;
use App\Model\Agency\ReviewsSummary;
use App\Model\Locale\AgencyReviewsSummary;
use App\Model\Locale\View;
use App\Repository\Locale\CityLocaleRepository;
use App\Repository\Locale\DistrictLocaleRepository;
use App\Repository\Locale\LocaleRepository;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;

class LocaleService
{
    private EntityManagerInterface $entityManager;
    private LocaleFactory $localeFactory;
    private LocaleRepository $localeRepository;
    private CityLocaleRepository $cityLocaleRepository;
    private DistrictLocaleRepository $districtLocaleRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        LocaleFactory $localeFactory,
        LocaleRepository $localeRepository,
        CityLocaleRepository $cityLocaleRepository,
        DistrictLocaleRepository $districtLocaleRepository
    ) {
        $this->entityManager = $entityManager;
        $this->localeFactory = $localeFactory;
        $this->localeRepository = $localeRepository;
        $this->cityLocaleRepository = $cityLocaleRepository;
        $this->districtLocaleRepository = $districtLocaleRepository;
    }

    public function getViewBySlug(string $slug): View
    {
        $locale = $this->localeRepository->findOnePublishedBySlug($slug);

        return $this->localeFactory->createViewFromEntity($locale);
    }

    public function findOrCreateByCity(City $city): CityLocale
    {
        $cityLocale = $this->cityLocaleRepository->findOneNullableByCity($city);

        if (null !== $cityLocale) {
            return $cityLocale;
        }

        $cityLocale = $this->localeFactory->createCityLocaleEntity($city);

        $this->entityManager->persist($cityLocale);
        $this->entityManager->flush();

        return $cityLocale;
    }

    public function findOrCreateByDistrict(District $district): DistrictLocale
    {
        $districtLocale = $this->districtLocaleRepository->findOneNullableByDistrict($district);

        if (null !== $districtLocale) {
            return $districtLocale;
        }

        $districtLocale = $this->localeFactory->createDistrictLocaleEntity($district);

        $this->entityManager->persist($districtLocale);
        $this->entityManager->flush();

        return $districtLocale;
    }

    // TODO replace with version in factory
    public function getAgencyReviewsSummary(Locale $locale): AgencyReviewsSummary
    {
        $agencies = [];
        /** @var TenancyReview $tenancyReview */
        foreach ($locale->getPublishedTenancyReviewsWithPublishedAgency() as $tenancyReview) {
            $agency = $tenancyReview->getAgency();
            if (null === $agency) {
                throw new LogicException(sprintf('Review %s has no agency. ', $tenancyReview->getId()));
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
