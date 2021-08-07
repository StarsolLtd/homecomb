<?php

namespace App\DataFixtures;

use App\Entity\Locale\Locale;
use App\Entity\Review;
use App\Util\ReviewHelper;
use Doctrine\Persistence\ObjectManager;

class ReviewFixtures extends AbstractDataFixtures
{
    private ReviewHelper $reviewHelper;

    public function __construct(
        ReviewHelper $reviewHelper
    ) {
        $this->reviewHelper = $reviewHelper;
    }

    protected function getEnvironments(): array
    {
        return ['dev', 'prod'];
    }

    protected function doLoad(ObjectManager $manager): void
    {
        /** @var Locale $cambridgeLocale */
        $cambridgeLocale = $this->getReference('locale-Cambridge');

        $reviews = [];

        $reviews[] = (new Review\LocaleReview())
            ->setLocale($cambridgeLocale)
            ->setAuthor('Helena Smithdon')
            ->setTitle('A mostly enjoyable city to live in')
            ->setContent('I had a delightful time living in Cambridge. There is much to do.')
            ->setOverallStars(4)
        ;

        foreach ($reviews as $review) {
            $review->setPublished(true);
            $review->setSlug($this->reviewHelper->generateSlug($review));
            $manager->persist($review);

            $this->addReference('review-'.$review->getSlug(), $review);
        }

        $manager->flush();
    }
}
