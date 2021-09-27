<?php

namespace App\Tests\E2E;

use Symfony\Component\Panther\Client as PantherClient;
use Symfony\Component\Panther\PantherTestCase;

final class UpdateAgency extends PantherTestCase
{
    use AgencyAdminTrait;

    private const TIMEOUT = 3;
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

        $this->loginAgencyAdmin($client);

        $client->waitFor('#home', self::TIMEOUT);

        $this->navigateToForm($client);

        $crawler = $client->waitFor('#update-agency-form', self::TIMEOUT);

        $submitButton = $crawler->selectButton('Update your agency details');

        $form = $submitButton->form();

        $form['externalUrl'] = 'http://testerton.co.uk';

        $submitButton->click();

        $client->waitFor('.alert-success', self::TIMEOUT);
    }

    private function navigateToForm(PantherClient $client): void
    {
        $crawler = $this->navigateToDashboard($client);

        $this->openHeaderNavBarIfClosed($crawler);

        $client->waitForVisibility('.update-agency-link', self::TIMEOUT);

        $crawler->filter('.update-agency-link')->click();
    }
}
