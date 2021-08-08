<?php

namespace App\Tests\E2E;

use Symfony\Component\Panther\PantherTestCase;

class SearchForPropertyAndReview extends PantherTestCase
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
        $crawler = $client->request('GET', $this->baseUrl.'/');

        $this->assertPageTitleContains('HomeComb');

        // TODO Select first visible, as presently this test will only pass on mobile view
        $this->propertyAutocomplete($client, '#home .property-autocomplete:first-child');

        $client->waitFor('.property-view', self::TIMEOUT);

        $this->assertPageTitleContains('249 Victoria Road');

        $crawler->selectButton('Yes! I want to write a review')->click();

        $client->waitFor('#review-tenancy-form', self::TIMEOUT);

        $submitButton = $crawler->selectButton('Share your tenancy review');

        $form = $submitButton->form();

        $form['reviewerEmail'] = 'mr.roboto@starsol.co.uk';
        $form['reviewerName'] = 'Good Robot';
        $form['agencyName'] = 'Norwich Homes';
        $form['agencyBranch'] = 'Drayton';
        $form['reviewTitle'] = 'This was a nice place to live';
        $form['reviewContent'] = 'There were some sunflowers in the garden';
        $crawler->filter('input[name=agreeTerms]')->click();
        $this->assertEquals('true', $crawler->filter('input[name=agreeTerms]')->attr('checked'));

        $client->submitForm('Share your tenancy review');

        $client->waitFor('.review-completed-thank-you', self::TIMEOUT);
        $client->waitForInvisibility('#review-tenancy-form', self::TIMEOUT);
        $client->waitForVisibility('.navigate-to-city-locale-review-form', self::TIMEOUT);
    }
}
