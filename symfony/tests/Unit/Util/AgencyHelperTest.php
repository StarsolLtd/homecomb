<?php

namespace App\Tests\Unit\Util;

use App\Entity\Agency;
use App\Util\AgencyHelper;
use PHPUnit\Framework\TestCase;

class AgencyHelperTest extends TestCase
{
    private AgencyHelper $agencyHelper;

    public function setUp(): void
    {
        $this->agencyHelper = new AgencyHelper();
    }

    public function testGenerateSlug(): void
    {
        $agency = (new Agency())->setName('Norwich Lettings');

        $result = $this->agencyHelper->generateSlug($agency);

        $expectedSlug = '58e5b6411117af';

        $this->assertEquals($expectedSlug, $result);
        $this->assertEquals($expectedSlug, $agency->getSlug());
    }
}
