<?php

namespace App\Tests\Functional\Controller\Api;

use App\DataFixtures\TestFixtures;
use App\Model\PropertySuggestion;
use App\Service\GetAddressService;
use function json_decode;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AgencyControllerTest extends WebTestCase
{
    use ProphecyTrait;

    public function testView(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/agency/'.TestFixtures::TEST_AGENCY_SLUG);

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals('Testerton Lettings', $content['name']);
        $this->assertEquals(TestFixtures::TEST_AGENCY_SLUG, $content['slug']);
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
}
