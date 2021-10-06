<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Factory\AgencyFactory;
use App\Factory\FlatModelFactory;
use App\Model\Agency\CreateAgencyInput;
use App\Model\Branch\Flat as FlatBranch;
use App\Util\AgencyHelper;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class AgencyFactoryTest extends TestCase
{
    use ProphecyTrait;

    private AgencyFactory $agencyFactory;

    private ObjectProphecy $agencyHelper;
    private ObjectProphecy $flatModelFactory;

    public function setUp(): void
    {
        $this->agencyHelper = $this->prophesize(AgencyHelper::class);
        $this->flatModelFactory = $this->prophesize(FlatModelFactory::class);

        $this->agencyFactory = new AgencyFactory(
            $this->agencyHelper->reveal(),
            $this->flatModelFactory->reveal(),
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

    public function testCreateViewFromEntity(): void
    {
        $branch1 = (new Branch())->setName('Holt');
        $flatBranch1 = $this->prophesize(FlatBranch::class);
        $branch2 = (new Branch())->setName('Sheringham');
        $flatBranch2 = $this->prophesize(FlatBranch::class);
        $agency = (new Agency())->setName('Gresham Homes')->setSlug('abcdef123456')->addBranch($branch1)->addBranch($branch2);

        $this->flatModelFactory->getBranchFlatModel($branch1)
            ->shouldBeCalledOnce()
            ->willReturn($flatBranch1);

        $this->flatModelFactory->getBranchFlatModel($branch2)
            ->shouldBeCalledOnce()
            ->willReturn($flatBranch2);

        $view = $this->agencyFactory->createViewFromEntity($agency);

        $this->assertEquals('Gresham Homes', $view->getName());
        $this->assertEquals('abcdef123456', $view->getSlug());
        $this->assertCount(2, $view->getBranches());
    }

    public function testCreateEntityByName1(): void
    {
        $name = 'Devon Homes';

        $this->agencyHelper->generateSlug(Argument::type(Agency::class))
            ->shouldBeCalledOnce()
            ->willReturn('test-agency-slug');

        $agency = $this->agencyFactory->createEntityByName($name);

        $this->assertEquals($name, $agency->getName());
        $this->assertEquals('test-agency-slug', $agency->getSlug());
    }
}
