<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Agency;
use App\Entity\Branch;
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

    public function testCreateViewFromEntity(): void
    {
        $branch1 = (new Branch())->setName('Holt')->setSlug('holtslug111');
        $branch2 = (new Branch())->setName('Sheringham')->setSlug('sheringhamslug222');
        $agency = (new Agency())->setName('Gresham Homes')->setSlug('abcdef123456')->addBranch($branch1)->addBranch($branch2);

        $view = $this->agencyFactory->createViewFromEntity($agency);

        $this->assertEquals('Gresham Homes', $view->getName());
        $this->assertEquals('abcdef123456', $view->getSlug());
        $this->assertCount(2, $view->getBranches());
        $this->assertEquals('Holt', $view->getBranches()[0]->getName());
        $this->assertEquals('holtslug111', $view->getBranches()[0]->getSlug());
        $this->assertEquals('Sheringham', $view->getBranches()[1]->getName());
        $this->assertEquals('sheringhamslug222', $view->getBranches()[1]->getSlug());
    }
}
