<?php

namespace App\Tests\E2E;

use Symfony\Component\Panther\PantherTestCase;

class ReviewSolicitationResponse extends PantherTestCase
{
    private const TIMEOUT = 5;

    public function testLoadHomePageAndSearchForProperty(): void
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/review-your-tenancy/73d2d50d17e8c1bbb05b8fddb3918033f2daf589');

        $this->assertPageTitleContains('HomeComb');

        $client->waitFor('#review-tenancy-form-submit', self::TIMEOUT);

        $h1 = $crawler->filter('h1');
        $this->assertEquals('Hello Anna!', $h1->text());

        $reviewerEmailInput = $crawler->filter('input[name=reviewerEmail]');
        $this->assertEquals('anna.testinova@starsol.co.uk', $reviewerEmailInput->attr('value'));

        $reviewerNameInput = $crawler->filter('input[name=reviewerName]');
        $this->assertEquals('Anna Testinova', $reviewerNameInput->attr('value'));

        $agencyInfoInput = $crawler->filter('input[name=agencyInfo]');
        $this->assertEquals('Cambridge Residential - Arbury', $agencyInfoInput->attr('value'));

        $form = $crawler->selectButton('review-tenancy-form-submit')->form();

        $form['reviewTitle'] = 'This was a nice place to live';
        $form['reviewContent'] = 'There were some sunflowers in the garden';

        $crawler = $crawler->selectButton('review-tenancy-form-submit')->click();

        $client->waitFor('.review-completed-thank-you', self::TIMEOUT);
    }
}
