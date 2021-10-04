<?php

namespace App\Service;

use App\Entity\Locale\Locale;
use App\Entity\TenancyReview;
use App\Factory\LocaleFactory;
use App\Model\Agency\ReviewsSummary;
use App\Model\Locale\AgencyReviewsSummary;
use App\Model\Locale\LocaleSearchResults;
use App\Repository\Locale\LocaleRepository;
use LogicException;

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
