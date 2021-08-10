<?php

namespace App\Tests\Functional\Controller;

use App\DataFixtures\TestFixtures;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CityControllerTest extends WebTestCase
{
    public function testViewBySlug(): void
    {
        $client = static::createClient();

        $client->request('GET', '/c/'.TestFixtures::TEST_CITY_KINGS_LYNN_SLUG);

        $this->assertEquals(Response::HTTP_MOVED_PERMANENTLY, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('/l/88b5dd5f8b7', $client->getResponse()->getContent());
    }
}
