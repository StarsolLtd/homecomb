<?php

namespace App\Tests\Unit\Util;

use App\Service\GetAddressService;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class GetAddressServiceTest extends TestCase
{
    use ProphecyTrait;

    private GetAddressService $getAddressService;

    private $client;

    public function setUp(): void
    {
        $this->client = $this->prophesize(HttpClientInterface::class);

        $this->getAddressService = new GetAddressService(
            $this->client->reveal()
        );
    }

    public function testGetAddress(): void
    {
        $vendorId = 'testvendorid';

        $response = $this->prophesize(ResponseInterface::class);

        $response->getContent()
            ->shouldBeCalledOnce()
            ->willReturn('{"postcode":"B63 4PT","latitude":52.443748474121094,"longitude":-2.0535950660705566,"formatted_address":["28 Fairfield Road","","","Halesowen","West Midlands"],"thoroughfare":"Fairfield Road","building_name":"","sub_building_name":"","sub_building_number":"","building_number":"28","line_1":"28 Fairfield Road","line_2":"","line_3":"","line_4":"","locality":"","town_or_city":"Halesowen","county":"West Midlands","district":"Dudley","country":"England","residential":true}');

        $this->client->request('GET', Argument::type('string'))
            ->shouldBeCalledOnce()
            ->willReturn($response);

        $vendorProperty = $this->getAddressService->getAddress($vendorId);

        $this->assertEquals('28 Fairfield Road', $vendorProperty->getAddressLine1());
        $this->assertEmpty($vendorProperty->getAddressLine2());
        $this->assertEmpty($vendorProperty->getAddressLine3());
        $this->assertEmpty($vendorProperty->getAddressLine4());
        $this->assertEquals('Halesowen', $vendorProperty->getCity());
        $this->assertEquals('West Midlands', $vendorProperty->getCounty());
        $this->assertEquals('Dudley', $vendorProperty->getDistrict());
        $this->assertEquals('B63 4PT', $vendorProperty->getPostcode());
        $this->assertTrue($vendorProperty->isResidential());
    }
}
