<?php

namespace App\Service;

use App\Model\PropertySuggestion;
use App\Model\VendorProperty;
use GuzzleHttp\Client;
use function json_decode;

class GetAddressService
{
    private Client $client;
    private string $apiKey = 'S2h3muKaRE-RBB4FYHSPag29280';

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @return PropertySuggestion[]
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function autocomplete(string $term): array
    {
        $uri = 'https://api.getAddress.io/autocomplete/'.$term.'?api-key='.$this->apiKey.'&top=10';

        $response = $this->client->request('GET', $uri);

        $results = json_decode($response->getBody()->getContents(), true)['suggestions'];

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

        $response = $this->client->request('GET', $uri);

        $result = json_decode($response->getBody()->getContents(), true);

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
}
