<?php

namespace App\Service;

use App\Exception\FailureException;
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
    private string $apiKey = 'S2h3muKaRE-RBB4FYHSPag29280';

    public function __construct(
        LoggerInterface $logger,
        HttpClientInterface $client
    ) {
        $this->client = $client;
        $this->logger = $logger;
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

    public function getAddress(string $vendorId): VendorProperty
    {
        $uri = 'https://api.getAddress.io/get/'.$vendorId.'?api-key='.$this->apiKey;

        try {
            $response = $this->client->request('GET', $uri);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Exception thrown finding addresses by postcode: '.$e->getMessage());

            throw new FailureException('Retrieval of property data from API failed.');
        }

        $result = json_decode($response->getContent(), true);

        return new VendorProperty(
            $vendorId,
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

    /**
     * @return VendorProperty[]
     */
    public function find(string $inputPostcode): array
    {
        $inputPostcode = preg_replace('/[^A-Za-z0-9 ]/', '', trim(strtolower($inputPostcode)));

        $uri = 'https://api.getaddress.io/find/'.$inputPostcode.'?api-key='.$this->apiKey.'&expand=true';

        try {
            $response = $this->client->request('GET', $uri);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Exception thrown finding addresses by postcode: '.$e->getMessage());

            return [];
        }

        $result = json_decode($response->getContent(), true);

        $postcode = $result['postcode'];
        $latitude = $result['latitude'];
        $longitude = $result['longitude'];

        $vendorProperties = [];

        foreach ($result['addresses'] as $address) {
            // TODO factory
            $vendorProperties[] = new VendorProperty(
                'TODO', // TODO
                $address['line_1'],
                $address['line_2'],
                $address['line_3'],
                $address['line_4'],
                $address['locality'],
                $address['town_or_city'],
                $address['county'],
                $address['district'],
                $address['country'],
                $postcode,
                $latitude,
                $longitude,
                $address['residential'] ?? false // TODO make nullable
            );
        }

        return $vendorProperties;
    }
}
