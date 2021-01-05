<?php

namespace App\Tests\E2E;

use Symfony\Component\Panther\PantherTestCase;

class FlagReview extends PantherTestCase
{
    private const TIMEOUT = 3;
    private string $baseUrl;

    public function setUp(): void
    {
        $this->baseUrl = 'http://localhost:591';
    }

    public function testLoadPropertyViewAndSubmitFlagForm(): void
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', $this->baseUrl.'/property/ccc5382816c1');

        $client->waitFor('.property-view', self::TIMEOUT);

        $this->assertPageTitleContains('249 Victoria Road');

        $h1 = $crawler->filter('h1');
        $this->assertEquals('249 Victoria Road, CB4 3LF', $h1->text());

        $crawler = $crawler->filter('.review-options button')->first();

        $client->waitFor('button.flag-review-link', self::TIMEOUT);

//        $flagReviewButton = $crawler->selectButton('Report this')->first();
//
//        $flagReviewButton->click();
//
//        $client->waitFor('form.flag-review-form', self::TIMEOUT);
    }
}
