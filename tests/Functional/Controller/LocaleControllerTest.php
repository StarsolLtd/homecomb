<?php

namespace App\Tests\Functional\Controller;

use App\DataFixtures\TestFixtures;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LocaleControllerTest extends WebTestCase
{
    public function testViewBySlug(): void
    {
        $client = static::createClient();

        $client->request('GET', '/l/'.TestFixtures::TEST_LOCALE_SLUG);

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
}
