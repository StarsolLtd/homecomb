<?php

namespace App\Tests\E2E;

use App\DataFixtures\TestFixtures;
use Symfony\Component\Panther\Client as PantherClient;

trait LoginTrait
{
    private function loginStandardUser(PantherClient $client): void
    {
        $crawler = $client->request('GET', $this->baseUrl.'/login');

        $client->waitFor('input[name=email]', self::TIMEOUT);

        $submitButton = $crawler->selectButton('Log in');

        $form = $submitButton->form();

        $this->assertEmpty($crawler->filter('input[name=email]')->attr('value'));
        $this->assertEmpty($crawler->filter('input[name=password]')->attr('value'));

        $form['email'] = TestFixtures::TEST_USER_STANDARD_2_EMAIL;
        $form['password'] = 'Password2';

        $client->submitForm('Log in');
    }
}
