<?php

namespace App\Service;

use App\Exception\FailureException;
use App\Factory\PropertyFactory;
use App\Model\Property\PostcodeProperties;
use App\Model\Property\PropertySuggestion;
use App\Model\Property\VendorProperty;
use function json_decode;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GetAddressService
{
    private LoggerInterface $logger;
    private HttpClientInterface $client;
    private PropertyFactory $propertyFactory;
    private string $apiKey = 'S2h3muKaRE-RBB4FYHSPag29280';

    public function __construct(
        LoggerInterface $logger,
        HttpClientInterface $client,
        PropertyFactory $propertyFactory
    ) {
        $this->client = $client;
        $this->logger = $logger;
        $this->propertyFactory = $propertyFactory;
    }

    /**
     * @return PropertySuggestion[]
     */
    public function autocomplete(string $term): array
    {
        $uri = 'https://api.getAddress.io/autocomplete/'.$term.'?api-key='.$this->apiKey.'&top=10';

        try {
            $response = $this->client->request('GET', $uri);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Exception thrown finding autocompletion options for search term: '.$e->getMessage());

            return [];
        }

        $results = json_decode($response->getContent(), true)['suggestions'];

        $suggestions = [];

        foreach ($results as $result) {
            $suggestions[] = new PropertySuggestion(
                $result['address'],
                $result['id']
            );
        }

        return $suggestions;
    }

    public function getAddress(string $vendorPropertyId): VendorProperty
    {
        $uri = 'https://api.getAddress.io/get/'.$vendorPropertyId.'?api-key='.$this->apiKey;

        try {
            $response = $this->client->request('GET', $uri);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Exception thrown finding addresses by postcode: '.$e->getMessage());

            throw new FailureException('Retrieval of property data from API failed.');
        }

        $result = json_decode($response->getContent(), true);

        return new VendorProperty(
            $vendorPropertyId,
            $result['line_1'],
            $result['line_2'],
            $result['line_3'],
            $result['line_4'],
            $result['locality'],
            $result['town_or_city'],
            $result['county'],
            $result['district'],
            $result['country'],
            $result['postcode'],
            $result['latitude'],
            $result['longitude'],
            $result['residential']
        );
    }

    public function find(string $inputPostcode): PostcodeProperties
    {
        $inputPostcode = preg_replace('/[^A-Za-z0-9]/', '', trim($inputPostcode)) ?? '';

        $uri = 'https://api.getaddress.io/find/'.$inputPostcode.'?api-key='.$this->apiKey.'&expand=true';

        try {
            $response = $this->client->request('GET', $uri);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Exception thrown finding addresses by postcode: '.$e->getMessage());

            return new PostcodeProperties($inputPostcode, []);
        }

        $responseStatusCode = $response->getStatusCode();
        if ($responseStatusCode >= 400) {
            $this->logger->info('Error finding addresses by postcode. HTTP status code: '.$responseStatusCode);

            return new PostcodeProperties($inputPostcode, []);
        }

        return $this->propertyFactory->createPostcodePropertiesFromFindResponseContent($response->getContent());
    }
}
