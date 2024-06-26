<?php

namespace App\Tests\E2E;

use Symfony\Component\Panther\PantherTestCase;

final class FindPropertyByPostcode extends PantherTestCase
{
    use PropertyAutocompleteTrait;

    private const TIMEOUT = 10;
    private string $baseUrl;

    public function setUp(): void
    {
        $this->baseUrl = $_ENV['E2E_TEST_BASE_URL'] ?? 'http://localhost';
    }

    public function testLoadHomePageAndSearchForProperty(): void
    {
        $client = static::createPantherClient();

        // Load home page, search for property, get no results and click to find by postcode
        $crawler = $client->request('GET', $this->baseUrl.'/');

        $this->assertPageTitleContains('HomeComb');

        $this->propertyAutocomplete($client, '#home .property-autocomplete:first-child', 'ZZZZZZZZZZZ_TEST');

        // Load find property page
        $client->waitFor('.find-by-postcode', self::TIMEOUT);

        $h1 = $crawler->filter('h1');
        $this->assertEquals('Find an address by postcode', $h1->text());

        // Search for incomplete postcode and get error
        $submitButton = $crawler->selectButton('Search');

        $form = $submitButton->form();
        $form['postcode'] = 'PE31';
        $client->submitForm('Search');

        $client->waitFor('.invalid-feedback', self::TIMEOUT);

        // Search for non-existent postcode and get 0 results
        $form['postcode'] = 'PE31 8RC';
        $client->submitForm('Search');

        $client->waitFor('.find-by-postcode-results', self::TIMEOUT);

        $h3 = $crawler->filter('.find-by-postcode-results h3');
        $this->assertEquals('0 results found in PE31 8RC', $h3->text());

        // Search for postcode, check it gets capitalised, get results and click one
        $form['postcode'] = 'pe31 8rw';
        $client->submitForm('Search');

        $client->waitFor('.find-by-postcode-results', self::TIMEOUT);

        $h3 = $crawler->filter('.find-by-postcode-results h3');
        $this->assertEquals('35 results found in PE31 8RW', $h3->text());

        $propertyLink = $crawler->filter('.address a')->first();
        $propertyLink->click();

        $client->waitFor('.property-view', self::TIMEOUT);

        $postcode = $crawler->filter('.city');
        $this->assertEquals("King's Lynn", $postcode->text());
    }
}
