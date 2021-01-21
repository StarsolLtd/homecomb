<?php

namespace App\Tests\Functional\Controller\Api;

use App\DataFixtures\TestFixtures;
use function json_decode;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SurveyControllerTest extends WebTestCase
{
    public function testView(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/s/'.TestFixtures::TEST_SURVEY_SLUG);

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals(TestFixtures::TEST_SURVEY_SLUG, $content['slug']);
    }
}
