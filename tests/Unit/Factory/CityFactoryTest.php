<?php

namespace App\Tests\Unit\Factory;

use App\Entity\City;
use App\Factory\CityFactory;
use App\Util\CityHelper;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Factory\CityFactory
 */
class CityFactoryTest extends TestCase
{
    use ProphecyTrait;

    private CityFactory $cityFactory;

    private $cityHelper;

    public function setUp(): void
    {
        $this->cityHelper = $this->prophesize(CityHelper::class);

        $this->cityFactory = new CityFactory(
            $this->cityHelper->reveal()
        );
    }

    /**
     * @covers \App\Factory\CityFactory::createEntity
     */
    public function testCreateEntity1(): void
    {
        $this->cityHelper->generateSlug(Argument::type(City::class))
            ->shouldBeCalledOnce()
            ->willReturn('test-city-slug');

        $city = $this->cityFactory->createEntity('Coventry', 'Warwickshire', 'UK');

        $this->assertEquals('Coventry', $city->getName());
        $this->assertEquals('Warwickshire', $city->getCounty());
        $this->assertEquals('UK', $city->getCountryCode());
        $this->assertEquals('test-city-slug', $city->getSlug());
        $this->assertTrue($city->isPublished());
    }
}
