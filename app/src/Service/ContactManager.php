<?php

namespace App\Service;

use App\Model\ContactModel;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

/**
 * Gestionnaire des contacts FO
 */
class ContactManager
{
    /**
     * Client guzzle
     */
    protected Client $guzzle;

    public function __construct(private LoggerInterface $logger)
    {
        $this->guzzle = new Client(
            [
                'http_errors' => false
            ]
        );
    }
    /**
     * Initialise un contact et pré setter pour le formulaire
     */
    public function initializeContact(string $email = null, string $lastname = null, string $firstname = null): ContactModel
    {
        return (new ContactModel())->setEmail($email)
            ->setLastname($lastname)
            ->setFirstname($firstname);
    }
    
    /**
     * Contrôle si le formulaire est envoyé par une personne et non un robot
     */
    public function checkForm(string $googleRecaptchaSkey, string $token): bool
    {
        try {
            $res = $this->guzzle->post(
                'https://www.google.com/recaptcha/api/siteverify',
                [
                    'form_params' =>
                        [
                            'secret' => $googleRecaptchaSkey,
                            'response' => $token
                        ]
                ]
            );

            if ($res->getStatusCode() == '200') {
                $this->logger->info('GOOGLE RECAPTCHA', json_decode($res->getBody(), true));

                return json_decode($res->getBody(), true)['success'];
            } else {
                $this->logger->error('GOOGLE RECAPTCHA', json_decode($res->getBody(), true));

                return false;
            }
        } catch (\Exception $e) {
            $this->logger->error('GOOGLE RECAPTCHA ERROR', $e->getMessage());

            return false;
        }
    }
}
