<?php

namespace App\Tests\E2E;

use App\DataFixtures\TestFixtures;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Symfony\Component\Panther\PantherTestCase;

class CompleteSurvey extends PantherTestCase
{
    private const TIMEOUT = 39;
    private string $baseUrl;

    public function setUp(): void
    {
        $this->baseUrl = $_ENV['E2E_TEST_BASE_URL'] ?? 'http://localhost';
    }

    public function testCompleteSurvey(): void
    {
        $client = static::createPantherClient();

        $session = new Session(new MockFileSessionStorage());
        self::bootKernel()->getContainer()->set('session', $session);

        $crawler = $client->request('GET', $this->baseUrl.'/s/'.TestFixtures::TEST_SURVEY_SLUG);

        $client->waitFor('#question1', self::TIMEOUT);

        $this->assertPageTitleContains('Chocolate bars of the UK');

        $h1 = $crawler->filter('h1');
        $this->assertEquals('Chocolate bars of the UK', $h1->text());

        $this->assertEmpty($crawler->filter('textarea[name=content]')->attr('value'));

        $submitButton = $crawler->selectButton('Submit');
        $form = $submitButton->form();
        $form['content'] = 'It makes me feel nutty';
        $submitButton->click();

        $client->waitFor('#question2', self::TIMEOUT);

        $crawler->filter('input[label=Newsagent]')->click();
        $this->assertEquals('true', $crawler->filter('input[label=Newsagent]')->attr('checked'));
        $crawler->selectButton('Submit')->click();

        $client->waitFor('#question3', self::TIMEOUT);

        $crawler->filter('.scale-5 .rating-icon')->click();
        $crawler->selectButton('Submit')->click();

        $client->waitFor('.survey-completed-thank-you', self::TIMEOUT);
    }
}
