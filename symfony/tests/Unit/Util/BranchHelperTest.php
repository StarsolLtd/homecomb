<?php

namespace App\Tests\Unit\Util;

use App\Entity\Agency;
use App\Entity\Branch;
use App\Util\BranchHelper;
use PHPUnit\Framework\TestCase;

class BranchHelperTest extends TestCase
{
    private BranchHelper $branchHelper;

    public function setUp(): void
    {
        $this->branchHelper = new BranchHelper();
    }

    public function testGenerateSlug(): void
    {
        $agency = (new Agency())->setName('Norwich Lettings');
        $branch = (new Branch())->setName('Drayton')->setAgency($agency);

        $result = $this->branchHelper->generateSlug($branch);

        $expectedSlug = 'da97e7f6c0e80';

        $this->assertEquals($expectedSlug, $result);
        $this->assertEquals($expectedSlug, $branch->getSlug());
    }
}
