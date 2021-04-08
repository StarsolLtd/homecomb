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

        $this->propertyAutocomplete($client, '#propertySearch');

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

        $client->submitForm('Share your tenancy review');

        $client->waitFor('.review-completed-thank-you', self::TIMEOUT);
    }
}
