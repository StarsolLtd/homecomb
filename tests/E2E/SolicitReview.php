<?php

namespace App\Tests\E2E;

use Symfony\Component\Panther\Client as PantherClient;
use Symfony\Component\Panther\PantherTestCase;

class SolicitReview extends PantherTestCase
{
    use AgencyAdminTrait;
    use PropertyAutocompleteTrait;

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

        $crawler = $client->waitFor('#solicit-review-form', self::TIMEOUT);

        $submitButton = $crawler->selectButton('Request review');

        $form = $submitButton->form();

        $this->propertyAutocomplete($client);

        $form['branchSlug'] = 'branch101slug';
        $form['recipientFirstName'] = 'Katarina';
        $form['recipientLastName'] = 'Homcomova';
        $form['recipientEmail'] = 'katarina.homcomova@starsol.co.uk';

        $submitButton->click();

        $client->waitFor('.alert-success', self::TIMEOUT);
    }

    private function navigateToForm(PantherClient $client): void
    {
        $crawler = $this->navigateToDashboard($client);

        $this->openHeaderNavBarIfClosed($crawler);

        $client->waitForVisibility('.request-review-link', self::TIMEOUT);

        $crawler->filter('.request-review-link')->click();

        $client->waitFor('#solicit-review-form', self::TIMEOUT);
    }
}
