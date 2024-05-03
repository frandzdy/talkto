<?php

namespace App\Service;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

readonly class RecaptchaManager
{
    /**
     * Client guzzle.
     */
    protected Client $guzzle;

    public function __construct(private LoggerInterface $logger)
    {
        $this->guzzle = new Client(
            [
                'http_errors' => false,
            ]
        );
    }

    /**
     * Contrôle si le formulaire est envoyé par une personne et non un robot.
     */
    public function checkForm(string $googleRecaptchaSkey, string $token): bool
    {
        try {
            $res = $this->guzzle->post(
                'https://www.google.com/recaptcha/api/siteverify',
                [
                    'form_params' => [
                        'secret' => $googleRecaptchaSkey,
                        'response' => $token,
                    ],
                ]
            );

            if ('200' == $res->getStatusCode()) {
                $this->logger->info('GOOGLE RECAPTCHA', json_decode($res->getBody(), true));

                return json_decode($res->getBody(), true)['success'];
            }
            $this->logger->error('GOOGLE RECAPTCHA', json_decode($res->getBody(), true));

            return false;
        } catch (\Exception $exception) {
            $this->logger->error('GOOGLE RECAPTCHA ERROR', ['message' => $exception->getMessage()]);

            return false;
        }
    }
}
