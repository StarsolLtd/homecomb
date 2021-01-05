<?php

namespace App\Tests\E2E;

use Symfony\Component\Panther\PantherTestCase;

class SearchForProperty extends PantherTestCase
{
    private const TIMEOUT = 3;
    private string $baseUrl;

    public function setUp(): void
    {
        $this->baseUrl = 'http://localhost:591';
    }

    public function testLoadHomePageAndSearchForProperty(): void
    {
        $client = static::createPantherClient();
        $client->request('GET', $this->baseUrl.'/');

        $this->assertPageTitleContains('HomeComb');

        $client->waitFor('#propertySearch');
    }
}
