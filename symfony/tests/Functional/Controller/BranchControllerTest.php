<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class BranchControllerTest extends WebTestCase
{
    public function testViewBySlug(): void
    {
        $client = static::createClient();

        $client->request('GET', '/branch/73e112d28bf77');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html h1', 'Cambridge Residential - Arbury');
    }
}
