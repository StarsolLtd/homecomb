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

        $reviewOptionsButton = $crawler->filter('.review-options button')->first();

        $reviewOptionsButton->click();

        $client->waitFor('button.flag-review-link', self::TIMEOUT);

        $crawler->selectButton('Report this')->click();

        $client->waitFor('form.flag-review-form', self::TIMEOUT);

        $submitButton = $crawler->selectButton('Send report');

        $form = $submitButton->form();

        $this->assertEmpty($crawler->filter('input[name=flagReviewContent]')->attr('value'));

        $form['flagReviewContent'] = "I don't link the font this review is written in";

        $this->assertNotEmpty($crawler->filter('input[name=flagReviewContent]')->attr('value'));

        $client->submitForm('Send report');

        $client->waitFor('.alert-success', self::TIMEOUT);

        $this->assertStringContainsString(
            'Your report was received successfully and will be checked by our moderation team shortly.',
            $crawler->filter('.alert-success')->text()
        );
    }
}
