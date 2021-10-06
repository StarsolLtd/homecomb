<?php

namespace App\Tests\Unit\Factory;

use App\Entity\BroadbandProvider;
use App\Factory\BroadbandProviderFactory;
use App\Util\BroadbandProviderHelper;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class BroadbandProviderFactoryTest extends TestCase
{
    use ProphecyTrait;

    private BroadbandProviderFactory $broadbandProviderFactory;

    private ObjectProphecy $broadbandProviderHelper;

    public function setUp(): void
    {
        $this->broadbandProviderHelper = $this->prophesize(BroadbandProviderHelper::class);

        $this->broadbandProviderFactory = new BroadbandProviderFactory(
            $this->broadbandProviderHelper->reveal(),
        );
    }

    public function testCreateEntityFromNameAndCountryCode1(): void
    {
        $name = 'Fastnet Limited';
        $countryCode = 'UK';

        $this->broadbandProviderHelper->generateSlug(Argument::type(BroadbandProvider::class))
            ->shouldBeCalledOnce()
            ->willReturn('test-provider-slug');

        $broadbandProvider = $this->broadbandProviderFactory->createEntityFromNameAndCountryCode($name, $countryCode);

        $this->assertEquals('Fastnet Limited', $broadbandProvider->getName());
        $this->assertEquals('UK', $broadbandProvider->getCountryCode());
        $this->assertEquals('test-provider-slug', $broadbandProvider->getSlug());
    }
}
