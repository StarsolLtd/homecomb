<?php

namespace App\Tests\E2E;

use Symfony\Component\Panther\PantherTestCase;

class Register extends PantherTestCase
{
    private const TIMEOUT = 5;
    private string $baseUrl;

    public function setUp(): void
    {
        $this->baseUrl = $_ENV['E2E_TEST_BASE_URL'] ?? 'http://localhost';
    }

    /**
     * Test registration form completion.
     */
    public function testRegister(): void
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', $this->baseUrl.'/register');

        $this->assertPageTitleContains('HomeComb');

        $client->waitFor('#register-form', self::TIMEOUT);

        $this->assertEmpty($crawler->filter('input[name=email]')->attr('value'));
        $this->assertEmpty($crawler->filter('input[name=firstName]')->attr('value'));
        $this->assertEmpty($crawler->filter('input[name=lastName]')->attr('value'));
        $this->assertEmpty($crawler->filter('input[name=plainPassword]')->attr('value'));
        $this->assertEmpty($crawler->filter('input[name=agreeTerms]')->attr('checked'));

        $submitButton = $crawler->selectButton('Create account');

        $form = $submitButton->form();

        $form['email'] = 'jelena.1999@starsol.co.uk';
        $form['firstName'] = 'Jelena';
        $form['lastName'] = 'Testa';
        $form['plainPassword'] = 'Test_Register_1999';
        $crawler->filter('input[name=agreeTerms]')->click();
        $this->assertEquals('true', $crawler->filter('input[name=agreeTerms]')->attr('checked'));

        $client->submitForm('Create account');

        $client->waitFor('#home', self::TIMEOUT);
    }
}
