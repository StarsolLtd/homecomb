<?php

namespace App\Tests\Unit\Util;

use App\Service\GetAddressService;
use function file_get_contents;
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

    public function testAutocomplete(): void
    {
        $response = $this->prophesize(ResponseInterface::class);

        $response->getContent()
            ->shouldBeCalledOnce()
            ->willReturn(file_get_contents(__DIR__.'/files/getAddress_autocomplete_response.json'));

        $this->client->request('GET', Argument::type('string'))
            ->shouldBeCalledOnce()
            ->willReturn($response);

        $propertySuggestions = $this->getAddressService->autocomplete('Loke Road');

        $this->assertCount(10, $propertySuggestions);
        $this->assertEquals("Well King's Lynn - Loke Road, Loke Road, King's Lynn", $propertySuggestions[0]->getAddress());
        $this->assertEquals('ODk3M2YyZDhkNTMzY2JmIENoSUppWDdLM3pPTDEwY1JQb2wtRWF2Z3JVdyAzM2Y4ZTQxZDRlNTM2NDM=', $propertySuggestions[0]->getVendorId());
        $this->assertEquals("Well King's Lynn - Loke Road, 38 Loke Road, King's Lynn, Norfolk", $propertySuggestions[1]->getAddress());
        $this->assertEquals('ZjI4M2FhYzgwOTVkNWFiIDUxMDkwMDQ4IDMzZjhlNDFkNGU1MzY0Mw==', $propertySuggestions[1]->getVendorId());
    }

    public function testGetAddress(): void
    {
        $response = $this->prophesize(ResponseInterface::class);

        $response->getContent()
            ->shouldBeCalledOnce()
            ->willReturn(file_get_contents(__DIR__.'/files/getAddress_get_response.json'));

        $this->client->request('GET', Argument::type('string'))
            ->shouldBeCalledOnce()
            ->willReturn($response);

        $vendorProperty = $this->getAddressService->getAddress('testvendorid');

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
