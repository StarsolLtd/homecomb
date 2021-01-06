<?php

namespace App\Tests\E2E;

use App\DataFixtures\TestFixtures;
use Symfony\Component\Panther\PantherTestCase;

class SolicitReview extends PantherTestCase
{
    private const TIMEOUT = 40;
    private string $baseUrl;

    public function setUp(): void
    {
        $this->baseUrl = $_ENV['E2E_TEST_BASE_URL'] ?? 'http://localhost';
    }

    /**
     * Test review request form.
     */
    public function testSolicitReview(): void
    {
        $client = static::createPantherClient();

        $this->login($client);
    }

    private function login($client)
    {
        $crawler = $client->request('GET', $this->baseUrl.'/login');

        $client->waitFor('input[name=email]', self::TIMEOUT);

        $submitButton = $crawler->selectButton('Log in');

        $form = $submitButton->form();

        $this->assertEmpty($crawler->filter('input[name=email]')->attr('value'));
        $this->assertEmpty($crawler->filter('input[name=password]')->attr('value'));

        $form['email'] = TestFixtures::TEST_USER_AGENCY_ADMIN_EMAIL;
        $form['password'] = 'Password2';

        $client->submitForm('Log in');

        $client->waitFor('#home', self::TIMEOUT);

        return $client;
    }
}
