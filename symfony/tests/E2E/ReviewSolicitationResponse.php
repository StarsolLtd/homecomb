<?php

namespace App\Tests\E2E;

use Symfony\Component\Panther\PantherTestCase;

class ReviewSolicitationResponse extends PantherTestCase
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
        $crawler = $client->request('GET', $this->baseUrl.'/review-your-tenancy/73d2d50d17e8c1bbb05b8fddb3918033f2daf589');

        $this->assertPageTitleContains('HomeComb');

        $client->waitFor('#review-tenancy-form-submit', self::TIMEOUT);

        $h1 = $crawler->filter('h1');
        $this->assertEquals('Hello Anna!', $h1->text());

        $this->assertEquals('anna.testinova@starsol.co.uk', $crawler->filter('input[name=reviewerEmail]')->attr('value'));
        $this->assertEquals('Anna Testinova', $crawler->filter('input[name=reviewerName]')->attr('value'));
        $this->assertEquals('Cambridge Residential - Arbury', $crawler->filter('input[name=agencyInfo]')->attr('value'));
        $this->assertEmpty($crawler->filter('input[name=reviewTitle]')->attr('value'));
        $this->assertEmpty($crawler->filter('textarea[name=reviewContent]')->attr('value'));

        $submitButton = $crawler->selectButton('Share your tenancy review');

        $form = $submitButton->form();

        $form['reviewTitle'] = 'This was a nice place to live';
        $form['reviewContent'] = 'There were some sunflowers in the garden';

        $client->submitForm('Share your tenancy review');

        $client->waitFor('.review-completed-thank-you', self::TIMEOUT);
    }
}
