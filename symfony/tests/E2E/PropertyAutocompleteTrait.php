<?php

namespace App\Tests\E2E;

use Symfony\Component\Panther\Client as PantherClient;

trait PropertyAutocompleteTrait
{
    private function propertyAutocomplete(PantherClient $client, string $selector = '#input-property', string $searchTerm = '249 Victo')
    {
        $crawler = $client->waitFor($selector, 3);

        $crawler->filter($selector)->sendKeys($searchTerm);

        $client->waitFor('.ui-autocomplete .ui-menu-item-wrapper', 15);

        $reviewOptionsButton = $crawler->filter('.ui-autocomplete .ui-menu-item-wrapper')->first();

        $reviewOptionsButton->click();
    }
}
