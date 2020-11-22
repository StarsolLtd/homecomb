<?php

namespace App\Service;

use App\Entity\Property;
use App\Model\LookupPropertyIdInput;
use App\Model\LookupPropertyIdOutput;
use App\Model\PropertySuggestion;
use App\Repository\PropertyRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use function json_decode;

class GetAddressService
{
    private Client $client;
    private string $apiKey = 'S2h3muKaRE-RBB4FYHSPag29280';

    public function __construct() {
        $this->client = new Client();
    }

    /**
     * @return PropertySuggestion[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function autocomplete(string $term): array
    {
        $response = $this->client->request('GET', 'https://api.getAddress.io/autocomplete/'.$term.'?api-key='.$this->apiKey);

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
}
