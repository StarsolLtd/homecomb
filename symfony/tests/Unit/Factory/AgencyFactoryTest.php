<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Agency;
use App\Factory\AgencyFactory;
use App\Model\Agency\CreateAgencyInput;
use App\Util\AgencyHelper;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class AgencyFactoryTest extends TestCase
{
    use ProphecyTrait;

    private AgencyFactory $agencyFactory;

    private $agencyHelper;

    public function setUp(): void
    {
        $this->agencyHelper = $this->prophesize(AgencyHelper::class);

        $this->agencyFactory = new AgencyFactory(
            $this->agencyHelper->reveal(),
        );
    }

    public function testCreateAgencyEntityFromCreateAgencyInputModel(): void
    {
        $createAgencyInput = new CreateAgencyInput(
            'Test Agency Name',
            'https://test.com/welcome',
            null,
            null
        );

        $this->agencyHelper->generateSlug(Argument::type(Agency::class))
            ->shouldBeCalledOnce()
            ->willReturn('ccc5382816c1');

        $agency = $this->agencyFactory->createAgencyEntityFromCreateAgencyInputModel($createAgencyInput);

        $this->assertEquals('Test Agency Name', $agency->getName());
        $this->assertEquals('https://test.com/welcome', $agency->getExternalUrl());
        $this->assertNull($agency->getPostcode());
    }
}
