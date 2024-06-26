<?php

namespace App\Tests\Functional\Controller\Api;

use App\DataFixtures\TestFixtures;
use App\Model\Property\PropertySuggestion;
use App\Service\GetAddressService;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class PropertyControllerTest extends WebTestCase
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

        $content = json_decode($response->getContent(), true);
        $this->assertCount(3, $content);
        $this->assertEquals('ZjAwMGE3YzY3ZTFhZDA0IDQwMjIwOTQgMzNmOGU0MWQ0ZTUzNjQz', $content[0]['id']);
        $this->assertNull($content[0]['slug']);
        $this->assertEquals('MjkwZjdjNDk4MTA4Njg5IDY1MDU2NzUgMzNmOGU0MWQ0ZTUzNjQz', $content[1]['id']);
        $this->assertNull($content[1]['slug']);
        $this->assertNull($content[2]['id']);
        $this->assertEquals(TestFixtures::TEST_PROPERTY_5_SLUG, $content[2]['slug']);
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
        $this->assertEquals('Terrence S.', $content['tenancyReviews'][0]['author']);
        $this->assertEquals(5, $content['tenancyReviews'][0]['stars']['overall']);
    }
}
