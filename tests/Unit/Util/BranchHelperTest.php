<?php

namespace App\Tests\Unit\Util;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Exception\DeveloperException;
use App\Util\BranchHelper;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @covers \App\Util\BranchHelper
 */
final class BranchHelperTest extends TestCase
{
    use ProphecyTrait;

    private BranchHelper $branchHelper;

    public function setUp(): void
    {
        $this->branchHelper = new BranchHelper();
    }

    /**
     * @covers \App\Util\BranchHelper::generateSlug
     */
    public function testGenerateSlug1(): void
    {
        $branch = $this->prophesize(Branch::class);
        $agency = $this->prophesize(Agency::class);

        $branch->getName()->shouldBeCalledOnce()->willReturn('Drayton');
        $branch->getAgency()->shouldBeCalledOnce()->willReturn($agency);
        $agency->getName()->shouldBeCalledOnce()->willReturn('Norwich Lettings');

        $result = $this->branchHelper->generateSlug($branch->reveal());

        $expectedSlug = 'da97e7f6c0e80';

        $this->assertEquals($expectedSlug, $result);
    }

    /**
     * @covers \App\Util\BranchHelper::generateSlug
     * Test throws DeveloperException when Branch has no name.
     */
    public function testGenerateSlug2(): void
    {
        $branch = $this->prophesize(Branch::class);
        $branch->getName()->shouldBeCalledOnce()->willReturn('');

        $this->expectException(DeveloperException::class);

        $this->branchHelper->generateSlug($branch->reveal());
    }
}
