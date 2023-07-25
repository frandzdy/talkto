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
     * Initialise un contact et pré setter pour le formulaire
     */
    public function initializeContact(string $email = null, string $lastname = null, string $firstname = null): ContactModel
    {
        return (new ContactModel())->setEmail($email)
            ->setLastname($lastname)
            ->setFirstname($firstname);
    }
}
