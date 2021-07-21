<?php

namespace App\Tests\Functional\Controller\Api;

use App\DataFixtures\TestFixtures;
use App\Model\Property\PropertySuggestion;
use App\Service\GetAddressService;
use function json_decode;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PropertyControllerTest extends WebTestCase
{
    use ProphecyTrait;

    public function testLookupSlugFromVendorId(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/property/lookup-slug-from-vendor-id?vendorPropertyId=ZmM5Yzc5MzMyODAyZTc4IDE3MDQ0OTcyIDMzZjhlNDFkNGU1MzY0Mw==');

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals('ccc5382816c1', $content['slug']);
    }

    public function testLookupSlugFromAddress(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/property/lookup-slug-from-address?addressLine1=249%20Victoria%20Road&postcode=CB4%203LF');

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals('ccc5382816c1', $content['slug']);
    }

    public function testSuggestProperty(): void
    {
        $client = static::createClient();
        $container = static::$kernel->getContainer();

        $getAddressService = $this->prophesize(GetAddressService::class);

        $getAddressService
            ->autocomplete('19 St Botolphs Clo')
            ->shouldBeCalledOnce()
            ->willReturn(
                [
                    new PropertySuggestion(
                        '19 St. Botolphs Close, Knottingley, West Yorkshire',
                        'ZjAwMGE3YzY3ZTFhZDA0IDQwMjIwOTQgMzNmOGU0MWQ0ZTUzNjQz'
                    ),
                    new PropertySuggestion(
                        "19 St. Botolphs Close, South Wootton, King's Lynn, Norfolk",
                        'MjkwZjdjNDk4MTA4Njg5IDY1MDU2NzUgMzNmOGU0MWQ0ZTUzNjQz'
                    ),
                ]
            );

        $container->set(GetAddressService::class, $getAddressService->reveal());

        $client->request('GET', '/api/property/suggest-property?term=19%20St%20Botolphs%20Clo');

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testView(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/property/'.TestFixtures::TEST_PROPERTY_1_SLUG);

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals(TestFixtures::TEST_PROPERTY_1_SLUG, $content['slug']);
        $this->assertEquals('Testerton Hall', $content['addressLine1']);
        $this->assertEquals('NR21 7ES', $content['postcode']);
        $this->assertEquals('Terrence S.', $content['reviews'][0]['author']);
        $this->assertEquals(5, $content['reviews'][0]['stars']['overall']);
    }
}
