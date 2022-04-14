<?php

namespace App\Tests\Unit\Service\Locale;

use App\Factory\LocaleFactory;
use App\Model\Locale\LocaleSearchResults;
use App\Repository\Locale\LocaleRepositoryInterface;
use App\Service\Locale\SearchService;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class SearchServiceTest extends TestCase
{
    use ProphecyTrait;

    private SearchService $searchService;

    private ObjectProphecy $localeFactory;
    private ObjectProphecy $localeRepository;

    public function setUp(): void
    {
        $this->localeFactory = $this->prophesize(LocaleFactory::class);
        $this->localeRepository = $this->prophesize(LocaleRepositoryInterface::class);

        $this->searchService = new SearchService(
            $this->localeFactory->reveal(),
            $this->localeRepository->reveal(),
        );
    }

    public function testSearch1()
    {
        $results = $this->prophesize(ArrayCollection::class);
        $localeSearchResults = $this->prophesize(LocaleSearchResults::class);

        $this->localeRepository->findBySearchQuery('king')->shouldBeCalledOnce()->willReturn($results);
        $this->localeFactory->createLocaleSearchResults('king', $results)->shouldBeCalledOnce()->willReturn($localeSearchResults);

        $output = $this->searchService->search('king');

        $this->assertEquals($output, $localeSearchResults->reveal());
    }
}
