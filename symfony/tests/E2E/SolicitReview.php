<?php

namespace App\Tests\E2E;

use App\DataFixtures\TestFixtures;
use App\Repository\UserRepository;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Panther\PantherTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SolicitReview extends PantherTestCase
{
    private const TIMEOUT = 5;
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
        $client = $this->createClientAndLoginUser(TestFixtures::TEST_USER_AGENCY_ADMIN_EMAIL);

        $crawler = $client->request('GET', $this->baseUrl.'/verified/request-review');

        $h1 = $crawler->filter('h1');
        $this->assertEquals('Request a review for Cambridge Residential', $h1->text());

        $client->waitFor('#solicit-review-form', self::TIMEOUT);
    }

    private function createClientAndLoginUser(string $username): object
    {
        $client = static::createPantherClient();

        /** @var UserRepository $userRepository */
        $userRepository = static::$container->get(UserRepository::class);
        $user = $userRepository->loadUserByUsername($username);

        $session = self::$container->get('session');

        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        return $client;
    }
}
