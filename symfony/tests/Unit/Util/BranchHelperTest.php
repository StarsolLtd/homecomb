<?php

namespace App\Tests\Unit\Util;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Exception\DeveloperException;
use App\Util\BranchHelper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Util\BranchHelper
 */
class BranchHelperTest extends TestCase
{
    private BranchHelper $branchHelper;

    public function setUp(): void
    {
        $this->branchHelper = new BranchHelper();
    }

    /**
     * @covers \App\Util\PropertyHelper::generateSlug
     */
    public function testGenerateSlug1(): void
    {
        $agency = (new Agency())->setName('Norwich Lettings');
        $branch = (new Branch())->setName('Drayton')->setAgency($agency);

        $result = $this->branchHelper->generateSlug($branch);

        $expectedSlug = 'da97e7f6c0e80';

        $this->assertEquals($expectedSlug, $result);
        $this->assertEquals($expectedSlug, $branch->getSlug());
    }

    /**
     * @covers \App\Util\PropertyHelper::generateSlug
     * Test throws DeveloperException when Branch has no name.
     */
    public function testGenerateSlug2(): void
    {
        $branch = new Branch();

        $this->expectException(DeveloperException::class);

        $this->branchHelper->generateSlug($branch);
    }
}
