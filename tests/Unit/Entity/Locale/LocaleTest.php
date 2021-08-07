<?php

namespace App\Tests\Unit\Entity\Locale;

use App\Entity\Locale\Locale;
use App\Tests\Unit\Entity\AbstractEntityTestCase;

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
