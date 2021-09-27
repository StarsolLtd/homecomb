<?php

namespace App\Tests\E2E;

use Symfony\Component\Panther\PantherTestCase;

final class NavigateViaLocaleAutocomplete extends PantherTestCase
{
    use PropertyAutocompleteTrait;

    private const TIMEOUT = 30;
    private string $baseUrl;

    public function setUp(): void
    {
        $this->baseUrl = $_ENV['E2E_TEST_BASE_URL'] ?? 'http://localhost';
    }

    /**
     * Test loading a locale page, then using the locale autocomplete to navigate to another locale page.
     */
    public function testLocaleReview(): void
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', $this->baseUrl.'/l/fakenham');

        $client->waitFor('.locale-name', self::TIMEOUT);
        $this->assertEquals('Fakenham', $crawler->filter('.locale-name')->getText());

        $crawler->filter('#localeSearch')->sendKeys('king');

        $client->waitFor('.ui-autocomplete .ui-menu-item-wrapper', self::TIMEOUT);

        $reviewOptionsButton = $crawler->filter('.ui-autocomplete .ui-menu-item-wrapper')->first();

        $reviewOptionsButton->click();

        $crawler = $client->waitFor('.locale-name', self::TIMEOUT);
        $this->assertEquals("King's Lynn", $crawler->filter('.locale-name')->getText());
    }
}
