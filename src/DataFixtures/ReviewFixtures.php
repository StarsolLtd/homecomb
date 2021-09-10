<?php

namespace App\DataFixtures;

use App\Entity\Locale\Locale;
use App\Entity\Review;
use App\Entity\User;
use App\Entity\Vote\LocaleReviewVote;
use App\Util\ReviewHelper;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReviewFixtures extends AbstractDataFixtures implements DependentFixtureInterface
{
    public function __construct(
        private ReviewHelper $reviewHelper,
    ) {
    }

    protected function getEnvironments(): array
    {
        return ['dev', 'demo'];
    }

    public function getDependencies(): array
    {
        return [
            DemoFixtures::class,
        ];
    }

    protected function doLoad(ObjectManager $manager): void
    {
        /** @var Locale $cambridgeLocale */
        $cambridgeLocale = $this->getReference('locale-Cambridge');

        /** @var User $user */
        $user = $this->getReference('user-'.DemoFixtures::USER_2);

        $reviews = [];

        $positiveVote1 = (new LocaleReviewVote())
            ->setPositive(true)
            ->setUser($user)
        ;
        assert($positiveVote1 instanceof LocaleReviewVote);
        $manager->persist($positiveVote1);

        $reviews[] = (new Review\LocaleReview())
            ->setLocale($cambridgeLocale)
            ->setAuthor('Helena Smithdon')
            ->setTitle('A mostly enjoyable city to live in')
            ->setContent('I had a delightful time living in Cambridge. There is much to do.')
            ->setOverallStars(4)
            ->addVote($positiveVote1)
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
