<?php

namespace App\Tests\Unit\Service;

use App\Factory\LocaleFactory;
use App\Model\Locale\LocaleSearchResults;
use App\Repository\Locale\LocaleRepository;
use App\Service\LocaleService;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \App\Service\LocaleService
 */
final class LocaleServiceTest extends TestCase
{
    use ProphecyTrait;

    private LocaleService $localeService;

    private ObjectProphecy $localeFactory;
    private ObjectProphecy $localeRepository;

    public function setUp(): void
    {
        $this->localeFactory = $this->prophesize(LocaleFactory::class);
        $this->localeRepository = $this->prophesize(LocaleRepository::class);

        $this->localeService = new LocaleService(
            $this->localeFactory->reveal(),
            $this->localeRepository->reveal(),
        );
    }

    /**
     * @covers \App\Service\LocaleService::search
     */
    public function testSearch1()
    {
        $results = $this->prophesize(ArrayCollection::class);
        $localeSearchResults = $this->prophesize(LocaleSearchResults::class);

        $this->localeRepository->findBySearchQuery('king')->shouldBeCalledOnce()->willReturn($results);
        $this->localeFactory->createLocaleSearchResults('king', $results)->shouldBeCalledOnce()->willReturn($localeSearchResults);

        $output = $this->localeService->search('king');

        $this->assertEquals($output, $localeSearchResults->reveal());
    }
}
