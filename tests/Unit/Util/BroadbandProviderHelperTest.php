<?php

namespace App\Tests\Unit\Util;

use App\Entity\BroadbandProvider;
use App\Exception\DeveloperException;
use App\Util\BroadbandProviderHelper;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class BroadbandProviderHelperTest extends TestCase
{
    use ProphecyTrait;

    private BroadbandProviderHelper $broadbandProviderHelper;

    public function setUp(): void
    {
        $this->broadbandProviderHelper = new BroadbandProviderHelper();
    }

    public function testGenerateSlug1(): void
    {
        $broadbandProvider = $this->prophesize(BroadbandProvider::class);
        $broadbandProvider->getName()->shouldBeCalledOnce()->willReturn('Fastnet Limited');
        $broadbandProvider->getCountryCode()->shouldBeCalledOnce()->willReturn('UK');

        $result = $this->broadbandProviderHelper->generateSlug($broadbandProvider->reveal());

        $expectedSlug = 'b614263946';

        $this->assertEquals($expectedSlug, $result);
    }

    /**
     * Test throws DeveloperException when there is no BroadbandProvider name.
     */
    public function testGenerateSlug2(): void
    {
        $broadbandProvider = $this->prophesize(BroadbandProvider::class);

        $this->expectException(DeveloperException::class);

        $broadbandProvider->getName()->shouldBeCalledOnce()->willReturn('');

        $this->broadbandProviderHelper->generateSlug($broadbandProvider->reveal());
    }
}
