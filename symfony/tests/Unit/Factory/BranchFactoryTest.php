<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Factory\BranchFactory;
use App\Model\Branch\CreateBranchInput;
use App\Util\BranchHelper;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class BranchFactoryTest extends TestCase
{
    use ProphecyTrait;

    private BranchFactory $branchFactory;

    private $branchHelper;

    public function setUp(): void
    {
        $this->branchHelper = $this->prophesize(BranchHelper::class);

        $this->branchFactory = new BranchFactory(
            $this->branchHelper->reveal(),
        );
    }

    public function testCreateBranchEntityFromCreateBranchInputModel(): void
    {
        $createBranchInput = new CreateBranchInput(
            'Test Branch Name',
            '0700 100 200',
            null,
            'sample'
        );

        $agency = new Agency();

        $this->branchHelper->generateSlug(Argument::type(Branch::class))
            ->shouldBeCalledOnce()
            ->willReturn('ccc5382816c1');

        $branch = $this->branchFactory->createBranchEntityFromCreateBranchInputModel($createBranchInput, $agency);

        $this->assertEquals($agency, $branch->getAgency());
        $this->assertEquals('Test Branch Name', $branch->getName());
        $this->assertEquals('0700 100 200', $branch->getTelephone());
        $this->assertNull($branch->getEmail());
    }
}
