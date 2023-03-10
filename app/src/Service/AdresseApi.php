<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

class AdresseApi
{
    /**
     * Client guzzle
     */
    protected Client $guzzle;

    /**
     * AdresseApi constructor.
s     */
    public function __construct(protected array $apiGouvConfig, protected LoggerInterface $logger)
    {
        $this->guzzle = new Client([
            'http_errors' => false
        ]);
    }

    /**
     * Prends en parametre une adresse et effectue une recherche sur l'API gouv
     * pour trouver les lat / long
     */
    public function searchAddress(string $city): ?array
    {
        $parameters = [
            'q' => $city,
            'city' => $city
        ];

        try {
            $time = microtime(true);
            $response = $this->guzzle->get(
                $this->apiGouvConfig['url'] . http_build_query($parameters)
            );

            $this->logger->info(
                'search',
                [
                    'time' => number_format(microtime(true) - $time, 3),
                    'parameters' => $parameters,
                    'code' => $response->getStatusCode(),
                    'response' => (string)$response->getBody()
                ]
            );
        } catch (GuzzleException $e) {
            $this->logger->error(
                'Erreur api',
                ['parameters' => $parameters, 'message' => $e->getMessage()]
            );
            return null;
        }

        $data = json_decode((string)$response->getBody(), true);

        if ($response->getStatusCode() == "200") {
            if (isset(current($data['features'])['geometry'])) {
                return [
                    'lat' => current($data['features'])['geometry']['coordinates'][1],
                    'lon' => current($data['features'])['geometry']['coordinates'][0]
                ];
            }
        } else {
            $this->logger->error(
                'Erreur api',
                [
                    'parameters' => $parameters,
                    'data' => $data
                ]
            );
        }

        return null;
    }
}
