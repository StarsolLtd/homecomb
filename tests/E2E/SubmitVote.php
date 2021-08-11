<?php

namespace App\Tests\E2E;

use App\DataFixtures\TestFixtures;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Symfony\Component\Panther\PantherTestCase;

class SubmitVote extends PantherTestCase
{
    use LoginTrait;

    private const TIMEOUT = 30;
    private string $baseUrl;

    public function setUp(): void
    {
        $this->baseUrl = $_ENV['E2E_TEST_BASE_URL'] ?? 'http://localhost';
    }

    public function testVoteButtonShowsLoginModalWhenLoggedOut(): void
    {
        $client = static::createPantherClient();

        $session = new Session(new MockFileSessionStorage());
        self::bootKernel()->getContainer()->set('session', $session);

        $crawler = $client->request('GET', $this->baseUrl.'/property/'.TestFixtures::TEST_PROPERTY_1_SLUG);
        $client->waitFor('.property-view', self::TIMEOUT);

        $positiveVotes = $crawler->filter('.positive-votes')->first();
        $this->assertEquals('2', $positiveVotes->text());

        $voteButton = $crawler->filter('.vote-button')->first();
        $voteButton->click();

        $client->waitForVisibility('.login-modal', self::TIMEOUT);

        $closeModalButton = $crawler->filter('.close-modal-button');
        $closeModalButton->click();

        $client->waitForInvisibility('.login-modal', self::TIMEOUT);

        $positiveVotes = $crawler->filter('.positive-votes')->first();
        $this->assertEquals('2', $positiveVotes->text());
    }

    public function testVoteButtonIncrementsVotesWhenLoggedIn(): void
    {
        $client = static::createPantherClient();

        $this->loginStandardUser($client);

        $session = new Session(new MockFileSessionStorage());
        self::bootKernel()->getContainer()->set('session', $session);

        $crawler = $client->request('GET', $this->baseUrl.'/property/'.TestFixtures::TEST_PROPERTY_1_SLUG);
        $client->waitFor('.property-view', self::TIMEOUT);

        $positiveVotes = $crawler->filter('.positive-votes')->first();
        $this->assertEquals('2', $positiveVotes->text());

        $voteButton = $crawler->filter('.vote-button')->first();
        $voteButton->click();

        $client->waitFor('.has-voted', self::TIMEOUT);

        $positiveVotes = $crawler->filter('.positive-votes')->first();
        $this->assertEquals('3', $positiveVotes->text());
    }
}
