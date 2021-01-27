<?php

namespace App\Service;

use App\Model\Property\PropertySuggestion;
use App\Model\Property\VendorProperty;
use function json_decode;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GetAddressService
{
    private HttpClientInterface $client;
    private string $apiKey = 'S2h3muKaRE-RBB4FYHSPag29280';

    public function __construct(
        HttpClientInterface $client
    ) {
        $this->client = $client;
    }

    /**
     * @return PropertySuggestion[]
     */
    public function autocomplete(string $term): array
    {
        $uri = 'https://api.getAddress.io/autocomplete/'.$term.'?api-key='.$this->apiKey.'&top=10';

        // TODO try/catch
        $response = $this->client->request('GET', $uri);

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

        // TODO try/catch
        $response = $this->client->request('GET', $uri);

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
     * @param string $inputPostcode
     * @return VendorProperty[]
     */
    public function find(string $inputPostcode): iterable
    {
        $inputPostcode = preg_replace('/[^A-Za-z0-9 ]/', '', trim(strtolower($inputPostcode)));

        $uri = 'https://api.getaddress.io/find/'.$inputPostcode.'?api-key='.$this->apiKey.'&expand=true';

        // TODO try/catch
        $response = $this->client->request('GET', $uri);

        $result = json_decode($response->getContent(), true);

        $postcode = $result['postcode'];
        $latitude = $result['latitude'];
        $longitude = $result['longitude'];

        foreach ($result['addresses'] as $address) {
            yield new VendorProperty(
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
                $address['residential']
            );
        }
    }
}
