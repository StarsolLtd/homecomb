<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Locale;

/**
 * @covers \App\Entity\Locale
 */
class LocaleTest extends AbstractEntityTestCase
{
    protected function getEntity(): Locale
    {
        return new Locale();
    }
}
