<?php

namespace App\Service;

use App\Entity\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

class AdresseApi
{
    /**
     * Client guzzle.
     */
    protected Client $guzzle;

    /**
     * AdresseApi constructor.
     */
    public function __construct(protected array $apiGouvConfig, protected LoggerInterface $addressLogger)
    {
        $this->guzzle = new Client([
            'http_errors' => false,
        ]);
    }

    /**
     * Prends-en paramètre une adresse et effectue une recherche sur l'API gouv
     * pour trouver les lat / long.
     */
    public function searchUserAddress(User $user): ?array
    {
        $parameters = [
            'q' => $user->getAddress(),
            'postcode' => $user->getZipCode(),
            'city' => $user->getCity(),
        ];

        try {
            $time = microtime(true);
            $response = $this->guzzle->get(
                $this->apiGouvConfig['url'].http_build_query($parameters)
            );

            $this->addressLogger->info(
                'search',
                [
                    'time' => number_format(microtime(true) - $time, 3),
                    'parameters' => $parameters,
                    'code' => $response->getStatusCode(),
                    'response' => (string) $response->getBody(),
                ]
            );
        } catch (GuzzleException $guzzleException) {
            $this->addressLogger->error(
                'Erreur api',
                ['parameters' => $parameters, 'message' => $guzzleException->getMessage()]
            );

            return null;
        }

        $data = json_decode((string) $response->getBody(), true);

        if ('200' == $response->getStatusCode()) {
            if (isset(current($data['features'])['geometry'])) {
                return [
                    'lat' => current($data['features'])['geometry']['coordinates'][1],
                    'lon' => current($data['features'])['geometry']['coordinates'][0],
                ];
            }
        } else {
            $this->addressLogger->error(
                'Erreur api',
                [
                    'parameters' => $parameters,
                    'data' => $data,
                ]
            );
        }

        return null;
    }
}
