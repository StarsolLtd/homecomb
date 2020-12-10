<?php

namespace App\Service;

use App\Entity\Locale;
use App\Entity\Review;
use App\Model\Agency\ReviewsSummary;
use App\Model\Locale\AgencyReviewsSummary;
use LogicException;

class LocaleService
{
    public function getAgencyReviewsSummary(Locale $locale): AgencyReviewsSummary
    {
        $agencies = [];
        /** @var Review $review */
        foreach ($locale->getPublishedReviewsWithPublishedAgency() as $review) {
            $agency = $review->getAgency();
            if (null === $agency) {
                throw new LogicException(sprintf('Review %s has no agency. ', $review->getId()));
            }

            $slug = $agency->getSlug();
            if (!isset($agencies[$slug])) {
                $agencies[$slug] = [
                    'name' => $agency->getName(),
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
            if (null !== $review->getAgencyStars()) {
                $rating = $review->getAgencyStars();
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
            $meanRating = round($agency['score'] / $agency['totalRated'], 2);

            $agencyReviewSummaries[] = new ReviewsSummary(
                $agencySlug,
                $agency['name'],
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

        return new AgencyReviewsSummary($agencyReviewSummaries, $reviewsCount, count($agencies));
    }
}
