<?php

namespace App\Tests\Unit\Factory;

use App\Factory\CityFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Factory\CityFactory
 */
class CityFactoryTest extends TestCase
{
    use ProphecyTrait;

    private CityFactory $cityFactory;

    public function setUp(): void
    {
        $this->cityFactory = new CityFactory();
    }

    /**
     * @covers \App\Factory\CityFactory::createEntity
     */
    public function testCreateEntity1(): void
    {
        $city = $this->cityFactory->createEntity('Coventry', 'Warwickshire', 'UK');

        $this->assertEquals('Coventry', $city->getName());
        $this->assertEquals('Warwickshire', $city->getCounty());
        $this->assertEquals('UK', $city->getCountryCode());
        $this->assertTrue($city->isPublished());
    }
}
