<?php

namespace App\Tests\Unit\Service;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Entity\Locale;
use App\Entity\Review;
use App\Factory\LocaleFactory;
use App\Model\Locale\View;
use App\Repository\LocaleRepository;
use App\Service\LocaleService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class LocaleServiceTest extends TestCase
{
    use ProphecyTrait;

    private LocaleService $localeService;

    private $localeFactory;
    private $localeRepository;

    public function setUp(): void
    {
        $this->localeFactory = $this->prophesize(LocaleFactory::class);
        $this->localeRepository = $this->prophesize(LocaleRepository::class);

        $this->localeService = new LocaleService(
            $this->localeFactory->reveal(),
            $this->localeRepository->reveal(),
        );
    }

    public function testGetViewBySlug(): void
    {
        $locale = (new Locale());
        $view = new View('localeslug', 'Alton');

        $this->localeRepository->findOnePublishedBySlug('localeslug')
            ->shouldBeCalledOnce()
            ->willReturn($locale);

        $this->localeFactory->createViewFromEntity($locale)
            ->shouldBeCalledOnce()
            ->willReturn($view);

        $output = $this->localeService->getViewBySlug('localeslug');

        $this->assertEquals($view, $output);
    }

    public function testGetAgencyReviewsSummary(): void
    {
        $locale = new Locale();

        $agency1 = (new Agency())->setName('Agency 1')->setPublished(true)->setSlug('ag1');
        $branch1 = (new Branch())->setAgency($agency1)->setPublished(true);
        $agency2 = (new Agency())->setName('Agency 2')->setPublished(true)->setSlug('ag2');
        $branch2 = (new Branch())->setAgency($agency2)->setPublished(true);
        $review1 = (new Review())->setBranch($branch1)->setPublished(true)->setAgencyStars(5);
        $review2 = (new Review())->setBranch($branch1)->setPublished(true)->setAgencyStars(2);
        $review3 = (new Review())->setBranch($branch2)->setPublished(true)->setAgencyStars(4);
        $review4 = (new Review())->setBranch($branch2)->setPublished(false)->setAgencyStars(1);
        $review5 = (new Review())->setBranch($branch2)->setPublished(true)->setAgencyStars(null);
        $locale->addReview($review1)->addReview($review2)->addReview($review3)->addReview($review4)->addReview($review5);

        $output = $this->localeService->getAgencyReviewsSummary($locale);

        // Agencies should be ordered by mean rating descending, so the higher rated Agency 2 should be first
        $this->assertEquals('Agency 2', $output->getAgencyReviewSummaries()[0]->getAgencyName());
        $this->assertEquals('ag2', $output->getAgencyReviewSummaries()[0]->getAgencySlug());
        $this->assertEquals(4, $output->getAgencyReviewSummaries()[0]->getMeanRating());
        $this->assertEquals(1, $output->getAgencyReviewSummaries()[0]->getRatedCount());
        $this->assertEquals(0, $output->getAgencyReviewSummaries()[0]->getFiveStarCount());
        $this->assertEquals(1, $output->getAgencyReviewSummaries()[0]->getFourStarCount());
        $this->assertEquals(0, $output->getAgencyReviewSummaries()[0]->getThreeStarCount());
        $this->assertEquals(0, $output->getAgencyReviewSummaries()[0]->getTwoStarCount());
        $this->assertEquals(0, $output->getAgencyReviewSummaries()[0]->getOneStarCount());
        $this->assertEquals(1, $output->getAgencyReviewSummaries()[0]->getUnratedCount());

        $this->assertEquals('Agency 1', $output->getAgencyReviewSummaries()[1]->getAgencyName());
        $this->assertEquals('ag1', $output->getAgencyReviewSummaries()[1]->getAgencySlug());
        $this->assertEquals(3.5, $output->getAgencyReviewSummaries()[1]->getMeanRating());
        $this->assertEquals(2, $output->getAgencyReviewSummaries()[1]->getRatedCount());
        $this->assertEquals(1, $output->getAgencyReviewSummaries()[1]->getFiveStarCount());
        $this->assertEquals(0, $output->getAgencyReviewSummaries()[1]->getFourStarCount());
        $this->assertEquals(0, $output->getAgencyReviewSummaries()[1]->getThreeStarCount());
        $this->assertEquals(1, $output->getAgencyReviewSummaries()[1]->getTwoStarCount());
        $this->assertEquals(0, $output->getAgencyReviewSummaries()[1]->getOneStarCount());
        $this->assertEquals(0, $output->getAgencyReviewSummaries()[1]->getUnratedCount());

        $this->assertEquals(3, $output->getReviewsCount());
        $this->assertEquals(2, $output->getAgenciesCount());
    }
}
