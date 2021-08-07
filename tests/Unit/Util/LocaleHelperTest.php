<?php

namespace App\Tests\Unit\Util;

use App\Entity\Locale\CityLocale;
use App\Util\LocaleHelper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Util\LocaleHelper
 */
class LocaleHelperTest extends TestCase
{
    private LocaleHelper $LocaleHelper;

    public function setUp(): void
    {
        $this->LocaleHelper = new LocaleHelper();
    }

    /**
     * @covers \App\Util\LocaleHelper::generateSlug
     */
    public function testGenerateSlug1(): void
    {
        $input = (new CityLocale())->setName('Ely');

        $actual = $this->LocaleHelper->generateSlug(($input));

        $this->assertEquals('53c21baa5be', $actual);
    }
}
