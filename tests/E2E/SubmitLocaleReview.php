<?php

namespace App\Tests\E2E;

use Symfony\Component\Panther\PantherTestCase;

class SubmitLocaleReview extends PantherTestCase
{
    use PropertyAutocompleteTrait;

    private const TIMEOUT = 30;
    private string $baseUrl;

    public function setUp(): void
    {
        $this->baseUrl = $_ENV['E2E_TEST_BASE_URL'] ?? 'http://localhost';
    }

    /**
     * Test loading the locale page, clicking the button to open the review form, and successfully posting a review.
     */
    public function testLocaleReview(): void
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', $this->baseUrl.'/l/fakenham');

        $crawler->selectButton('Yes! I want to write a review')->click();

        $crawler = $client->waitFor('#review-locale-form', self::TIMEOUT);

        $submitButton = $crawler->selectButton('Share your review');

        $form = $submitButton->form();

        $form['reviewerEmail'] = 'mr.roboto@starsol.co.uk';
        $form['reviewerName'] = 'Good Robot';
        $form['reviewTitle'] = 'There are 4 whole supermarkets here!';
        $form['reviewContent'] = 'My favourite is Tesco, I buy yoghurts there.';
        $crawler->filter('input[name=agreeTerms]')->click();
        $this->assertEquals('true', $crawler->filter('input[name=agreeTerms]')->attr('checked'));

        $client->submitForm('Share your review');

        $client->waitFor('.review-completed-thank-you', self::TIMEOUT);
    }
}
