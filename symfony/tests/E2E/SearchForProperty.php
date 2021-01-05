<?php

namespace App\Tests\E2E;

use Symfony\Component\Panther\PantherTestCase;

class SearchForProperty extends PantherTestCase
{
    private const TIMEOUT = 10;
    private string $baseUrl;

    public function setUp(): void
    {
        $this->baseUrl = 'http://localhost:591';
    }

    public function testLoadHomePageAndSearchForProperty(): void
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', $this->baseUrl.'/');

        $this->assertPageTitleContains('HomeComb');

        $client->waitFor('#propertySearch', self::TIMEOUT);

        $crawler->filter('#propertySearch')->sendKeys('249 Victo');

        $client->waitFor('.ui-autocomplete .ui-menu-item-wrapper', self::TIMEOUT);

        $reviewOptionsButton = $crawler->filter('.ui-autocomplete .ui-menu-item-wrapper')->first();

        $reviewOptionsButton->click();

        $client->waitFor('.property-view', self::TIMEOUT);

        $this->assertPageTitleContains('249 Victoria Road');
    }
}
