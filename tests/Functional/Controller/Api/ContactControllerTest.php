<?php

namespace App\Tests\Functional\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ContactControllerTest extends WebTestCase
{
    public function testSubmitContact(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/contact',
            [],
            [],
            [],
            '{"name":"Fiona Dutton","emailAddress":"fiona.dutton@starsol.co.uk","message":"This is a test.","captchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
}
