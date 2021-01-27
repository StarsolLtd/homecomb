<?php

namespace App\Tests\Unit\Service;

use App\Model\Property\VendorProperty;
use App\Service\GetAddressService;
use function file_get_contents;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\Exception\TimeoutException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @covers \App\Service\GetAddressService
 */
class GetAddressServiceTest extends TestCase
{
    use ProphecyTrait;

    private GetAddressService $getAddressService;

    private $logger;
    private $client;

    public function setUp(): void
    {
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->client = $this->prophesize(HttpClientInterface::class);

        $this->getAddressService = new GetAddressService(
            $this->logger->reveal(),
            $this->client->reveal()
        );
    }

    /**
     * @covers \App\Service\GetAddressService::autocomplete
     */
    public function testAutocomplete1(): void
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

    /**
     * @covers \App\Service\GetAddressService::getAddress
     */
    public function testGetAddress1(): void
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

    /**
     * @covers \App\Service\GetAddressService::getAddress
     */
    public function testFind1(): void
    {
        $response = $this->prophesize(ResponseInterface::class);

        $response->getContent()
            ->shouldBeCalledOnce()
            ->willReturn(file_get_contents(__DIR__.'/files/getAddress_find_expand_response.json'));

        $this->client->request('GET', Argument::type('string'))
            ->shouldBeCalledOnce()
            ->willReturn($response);

        $output = $this->getAddressService->find('NN1 3ER');

        $this->assertCount(70, $output);
        $this->assertContainsOnlyInstancesOf(VendorProperty::class, $output);
    }

    /**
     * @covers \App\Service\GetAddressService::getAddress
     * Test when exception is thrown calling API, error is logged and result is empty
     */
    public function testFind2(): void
    {
        $this->client->request('GET', Argument::type('string'))
            ->shouldBeCalledOnce()
            ->willThrow(TimeoutException::class);

        $this->logger->error(Argument::type('string'))
            ->shouldBeCalledOnce();

        $output = $this->getAddressService->find('NN1 3ER');

        $this->assertEmpty($output);
    }
}
