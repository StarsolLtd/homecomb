<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LocaleControllerTest extends WebTestCase
{
    public function testViewBySlug(): void
    {
        $client = static::createClient();

        $client->request('GET', '/l/cambridge');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html h1', 'Cambridge');
    }
}
