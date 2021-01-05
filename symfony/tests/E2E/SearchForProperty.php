<?php

namespace App\Tests\E2E;

use Symfony\Component\Panther\PantherTestCase;

class SearchForProperty extends PantherTestCase
{
    public function testLoadHomePageAndSearchForProperty(): void
    {
        $client = static::createPantherClient();
        $client->request('GET', '/');

        $this->assertPageTitleContains('HomeComb');

        $client->waitFor('#propertySearch');
    }
}
