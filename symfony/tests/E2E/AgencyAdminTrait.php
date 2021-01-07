<?php

namespace App\Tests\E2E;

use App\DataFixtures\TestFixtures;
use Symfony\Component\Panther\Client as PantherClient;
use Symfony\Component\Panther\DomCrawler\Crawler;

trait AgencyAdminTrait
{
    private function loginAgencyAdmin(PantherClient $client): void
    {
        $crawler = $client->request('GET', $this->baseUrl.'/login');

        $client->waitFor('input[name=email]', self::TIMEOUT);

        $submitButton = $crawler->selectButton('Log in');

        $form = $submitButton->form();

        $this->assertEmpty($crawler->filter('input[name=email]')->attr('value'));
        $this->assertEmpty($crawler->filter('input[name=password]')->attr('value'));

        $form['email'] = TestFixtures::TEST_USER_AGENCY_1_ADMIN_EMAIL;
        $form['password'] = 'Password2';

        $client->submitForm('Log in');
    }

    private function navigateToDashboard(PantherClient $client): Crawler
    {
        $crawler = $client->waitFor('.agency-admin-link', 3);

        $crawler->filter('.agency-admin-link')->click();

        $crawler = $client->waitFor('#dashboard', 3);

        return $crawler;
    }

    private function openHeaderNavBarIfClosed(Crawler $crawler): void
    {
        $navbarTogglerButton = $crawler->filter('.navbar-toggler')->first();
        if ($navbarTogglerButton->isDisplayed()) {
            $navbarTogglerButton->click();
        }
    }
}
