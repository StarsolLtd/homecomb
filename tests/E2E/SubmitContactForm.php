<?php

namespace App\Tests\E2E;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Symfony\Component\Panther\PantherTestCase;

final class SubmitContactForm extends PantherTestCase
{
    private const TIMEOUT = 30;
    private string $baseUrl;

    public function setUp(): void
    {
        $this->baseUrl = $_ENV['E2E_TEST_BASE_URL'] ?? 'http://localhost';
    }

    public function testSubmitContactForm(): void
    {
        $client = static::createPantherClient();

        $session = new Session(new MockFileSessionStorage());
        self::bootKernel()->getContainer()->set('session', $session);

        $crawler = $client->request('GET', $this->baseUrl.'/contact');

        $client->waitFor('#contact-form', self::TIMEOUT);

        $submitButton = $crawler->selectButton('Contact us');

        $form = $submitButton->form();
        $form['emailAddress'] = 'fiona.dutton@starsol.co.uk';
        $form['name'] = 'Fiona';
        $form['message'] = 'This is a test'."\n\n".'with a second paragraph.';

        $client->submitForm('Contact us');

        $client->waitFor('.alert-success', self::TIMEOUT);

        $this->assertStringContainsString(
            'Thank you, your message has been sent to us successfully.',
            $crawler->filter('.alert-success')->text()
        );
    }
}
