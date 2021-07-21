<?php

namespace App\Tests\E2E;

use App\DataFixtures\TestFixtures;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Symfony\Component\Panther\PantherTestCase;

class FlagReview extends PantherTestCase
{
    private const TIMEOUT = 39;
    private string $baseUrl;

    public function setUp(): void
    {
        $this->baseUrl = $_ENV['E2E_TEST_BASE_URL'] ?? 'http://localhost';
    }

    public function testLoadPropertyViewAndSubmitFlagForm(): void
    {
        $client = static::createPantherClient();

        $session = new Session(new MockFileSessionStorage());
        self::bootKernel()->getContainer()->set('session', $session);

        $crawler = $client->request('GET', $this->baseUrl.'/property/'.TestFixtures::TEST_PROPERTY_1_SLUG);

        $client->waitFor('.property-view', self::TIMEOUT);

        $this->assertPageTitleContains('Testerton Hall');

        $h1 = $crawler->filter('h1');
        $this->assertEquals('Testerton Hall, NR21 7ES', $h1->text());

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
