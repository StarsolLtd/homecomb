<?php

namespace App\Tests\Unit\Service\Locale;

use App\Entity\Locale\Locale;
use App\Factory\LocaleFactory;
use App\Model\Locale\View;
use App\Repository\Locale\LocaleRepository;
use App\Service\Locale\ViewService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class ViewServiceTest extends TestCase
{
    use ProphecyTrait;

    private ViewService $viewService;

    private ObjectProphecy $localeFactory;
    private ObjectProphecy $localeRepository;

    public function setUp(): void
    {
        $this->localeFactory = $this->prophesize(LocaleFactory::class);
        $this->localeRepository = $this->prophesize(LocaleRepository::class);

        $this->viewService = new ViewService(
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

        $output = $this->viewService->getViewBySlug('localeslug');

        $this->assertEquals($view, $output);
    }
}
