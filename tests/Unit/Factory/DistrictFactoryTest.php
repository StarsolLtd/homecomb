<?php

namespace App\Tests\Unit\Factory;

use App\Entity\District;
use App\Factory\DistrictFactory;
use App\Util\DistrictHelper;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Factory\DistrictFactory
 */
class DistrictFactoryTest extends TestCase
{
    use ProphecyTrait;

    private DistrictFactory $districtFactory;

    private $districtHelper;

    public function setUp(): void
    {
        $this->districtHelper = $this->prophesize(DistrictHelper::class);

        $this->districtFactory = new DistrictFactory(
            $this->districtHelper->reveal()
        );
    }

    /**
     * @covers \App\Factory\DistrictFactory::createEntity
     */
    public function testCreateEntity1(): void
    {
        $this->districtHelper->generateSlug(Argument::type(District::class))
            ->shouldBeCalledOnce()
            ->willReturn('test-district-slug');

        $district = $this->districtFactory->createEntity('Islington', null, 'UK');

        $this->assertEquals('Islington', $district->getName());
        $this->assertNull($district->getCounty());
        $this->assertEquals('UK', $district->getCountryCode());
        $this->assertEquals('test-district-slug', $district->getSlug());
        $this->assertTrue($district->isPublished());
    }
}
