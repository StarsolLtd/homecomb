<?php

namespace App\Tests\Functional\Controller\Api;

use App\DataFixtures\TestFixtures;
use function json_decode;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LocaleControllerTest extends WebTestCase
{
    public function testView(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/l/'.TestFixtures::TEST_LOCALE_SLUG);

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals('Fakenham', $content['name']);
        $this->assertEquals(TestFixtures::TEST_LOCALE_SLUG, $content['slug']);
        $this->assertEquals('Terrence S.', $content['tenancyReviews'][0]['author']);
    }

    public function testSearch1(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/locale-search?q=king');

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertCount(2, $content['locales']);

        $this->assertEquals(TestFixtures::TEST_CITY_LOCALE_KINGS_LYNN_SLUG, $content['locales'][0]['slug']);
        $this->assertEquals(TestFixtures::TEST_CITY_LOCALE_KINGSTON_UPON_THAMES_SLUG, $content['locales'][1]['slug']);
    }
}
