<?php

namespace App\Tests\E2E;

use Symfony\Component\Panther\PantherTestCase;

class TenancyReview extends PantherTestCase
{
    use PropertyAutocompleteTrait;

    private const TIMEOUT = 10;
    private string $baseUrl;

    public function setUp(): void
    {
        $this->baseUrl = $_ENV['E2E_TEST_BASE_URL'] ?? 'http://localhost';
    }

    /**
     * Test loading the home page, clicking the "Review your tenancy" button, and successfully posting a review.
     */
    public function testTenancyReview(): void
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', $this->baseUrl.'/');

        $crawler->selectButton('Review your tenancy')->click();

        $crawler = $client->waitFor('#review-tenancy-form', self::TIMEOUT);

        $submitButton = $crawler->selectButton('Share your tenancy review');

        $form = $submitButton->form();

        $this->propertyAutocomplete($client, '#input-property', '8 Ever Rea');

        $form['reviewerEmail'] = 'mr.roboto@starsol.co.uk';
        $form['reviewerName'] = 'Good Robot';
        $form['agencyName'] = 'Peak Residential';
        $form['agencyBranch'] = 'Ashbourne';
        $form['reviewTitle'] = 'This was a lovely place to reside';
        $form['reviewContent'] = 'I made friends with a rabbit in the garden';

        $client->submitForm('Share your tenancy review');

        $client->waitFor('.review-completed-thank-you', self::TIMEOUT);
    }
}
