<?php

namespace App\Tests\Controller\Api;

use function json_decode;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PropertyControllerTest extends WebTestCase
{
    public function testLookupSlugFromVendorId(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/property/lookup-slug-from-vendor-id?vendorPropertyId=ZmM5Yzc5MzMyODAyZTc4IDE3MDQ0OTcyIDMzZjhlNDFkNGU1MzY0Mw==');

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals('ccc5382816c1', $content['slug']);
    }
}
