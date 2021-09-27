<?php

namespace App\Tests\Unit\Util;

use App\Entity\Agency;
use App\Exception\DeveloperException;
use App\Util\AgencyHelper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Util\AgencyHelper
 */
final class AgencyHelperTest extends TestCase
{
    private AgencyHelper $agencyHelper;

    public function setUp(): void
    {
        $this->agencyHelper = new AgencyHelper();
    }

    /**
     * @covers \App\Util\AgencyHelper::generateSlug
     */
    public function testGenerateSlug1(): void
    {
        $agency = (new Agency())->setName('Norwich Lettings');

        $result = $this->agencyHelper->generateSlug($agency);

        $expectedSlug = '58e5b6411117af';

        $this->assertEquals($expectedSlug, $result);
        $this->assertEquals($expectedSlug, $agency->getSlug());
    }

    /**
     * @covers \App\Util\AgencyHelper::generateSlug
     * Test throws DeveloperException when there is no Agency name.
     */
    public function testGenerateSlug2(): void
    {
        $agency = new Agency();

        $this->expectException(DeveloperException::class);

        $this->agencyHelper->generateSlug($agency);
    }
}
