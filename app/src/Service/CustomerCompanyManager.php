<?php

namespace App\Service;

use App\Controller\Back\CustomerController;
use App\Entity\Civility;
use App\Entity\Country;
use App\Entity\CustomerAccount;
use App\Entity\CustomerCompany;
use App\Entity\CustomerCompanyAddress;
use App\Service\Api\Thotem as ThotemApi;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Gestionnaire des comptes entreprises BO
 */
class CustomerCompanyManager
{
    /**
     * Constructor.
     */
    public function __construct(
        private EntityManagerInterface $em,
        private SageManager $sageManager,
        private ThotemApi $thotem,
        private GoogleMapHandler $googleMapHandler,
        private LoggerInterface $sageLogger,
        private CartManager $cartManager,
        private RequestStack $requestStack
    ) {
    }

    /**
     * Initialise un compte d'entreprise
     */
    public function initializeCustomerCompany(?string $siret, ?string $email): CustomerCompany
    {
        $company = (new CustomerCompany())
            ->setLeaderCivility(Civility::CIVILITY_MEN)
            ->setSiret($siret)
            ->setBillingEmail($email)
            ->setCertificateEmail($email);

        $address = (new CustomerCompanyAddress())
            ->setCustomerCompany($company)
            ->setCountry($this->em->getRepository(Country::class)->findOneBy(['code' => 'FR']));

        $company->setMainAddress($address);

        return $company;
    }

    /**
     * Sauvegarde d'une société
     *
     * Retourne true si Sage a renvoyé des informations.
     */
    public function createOrSaveCompany(CustomerCompany $customerCompany): bool
    {
        if (!$customerCompany->getId()) {
            $this->em->persist($customerCompany);
        }
        $sageSucceeded = true;
        $oldSiret = $this->requestStack->getSession()->get(CustomerController::COMPANY_SIRET, null);
        $oldAddress = $this->requestStack->getSession()->get(CustomerController::COMPANY_ADDRESS, null);

        // mettre à jour les informations Sagecode
        // si on change le siret du compte et qu'il n'a pas encore de code Sage
        if ($oldSiret) {
            if ($oldSiret !== $customerCompany->getSiret() && empty($customerCompany->getSageCode())) {
                $customerFromSage = $this->sageManager->getCustomerCodeBySiret($customerCompany->getSiret());
                if ($customerFromSage) {
                    $customerCompany->setSageCode($customerFromSage['sageCode']);
                }
                $this->sageLogger->info('Update sageCode BO');
            }
        }
        // si on change l'adresse et que l'on ai un code sage alors on met à jour la géolocalisation de l'adresse
        if ($oldAddress) {
            // soit on a changé l'adresse et que le compte a un code sage ou
            // soit la longitude est null alors on recalcule
            if (
                (
                    $oldAddress !== $customerCompany->getMainAddress() &&
                    $customerCompany->getSageCode()
                ) || (
                    '0' == $customerCompany->getLon() ||
                    '0.0' == $customerCompany->getLon() ||
                    null === $customerCompany->getLon()
                )
            ) {
                $geometry = $this->googleMapHandler->searchCustomerCompanyAddress($customerCompany->getMainAddress());
                if ($geometry) {
                    $customerCompany->setLat($geometry['lat']);
                    $customerCompany->setLon($geometry['lon']);
                }
                $this->sageLogger->info(
                    'GMAP Géolocalisation BO',
                    [
                        'data' => $geometry
                    ]
                );
            }
        }
        // on supprime la session
        $this->requestStack->getSession()->remove(CustomerController::COMPANY_SIRET);
        $this->requestStack->getSession()->remove(CustomerController::COMPANY_ADDRESS);

        $dataSage = $this->updateAccountInfoFromSageBO($customerCompany);
        if ($dataSage) {
            $customerCompany->setSageCode($dataSage);
        } else {
            $sageSucceeded = false;
        }
        $this->sageLogger->info(
            'Mise à jour Sage BO',
            [
                'Réponse Sage' => $dataSage
            ]
        );

        $this->em->flush();

        return $sageSucceeded;
    }

    /**
     * Supprime une société
     */
    public function removeCompany(CustomerCompany $customerCompany): void
    {
        $customerCompany->setMainAddress(null);

        foreach ($customerCompany->getInvoices() as $invoice) {
            if ($invoice->getTransaction()) {
                $this->em->remove($invoice->getTransaction());
            }
            $invoice->setCustomerCompany(null);
            $this->em->remove($invoice);
        }

        foreach ($customerCompany->getAccounts() as $account) {
            // on récupère son panier et on les élements du panier et on le supprime
            $cart = $this->cartManager->getCart($account);
            if ($cart) {
                foreach ($cart->getItems() as $item) {
                    $this->em->remove($item);
                    $cart->removeItem($item);
                }
                if ($cart->getOrder()) {
                    $this->em->remove($cart->getOrder());
                }
                $this->em->remove($cart);
            }

            // on récupère son panier et on les élements du panier et on le supprime
            $orders = $this->cartManager->getOrders($account);
            if ($orders) {
                foreach ($orders as $order) {
                    $this->em->remove($order);
                }
            }

            // on retire la liaison avec la société et on supprime le compte si il n'a pas d'autre compte
            $customerCompany->removeAccount($account);
            $userToDelete = null;
            if ($account->getUser()->getAccounts()->count() === 1) {
                // Suppression des transactions de l'utilisateur
                foreach ($account->getUser()->getTransactions() as $transaction) {
                    $transaction->setUser(null);
                    $this->em->remove($transaction);
                }

                // suppression du compte utilisateur
                $userToDelete = $account->getUser();
            }
            $account->setUser(null);
            $this->em->remove($account);

            if ($userToDelete) {
                $this->em->remove($userToDelete);
            }
        }
        $this->em->flush();

        foreach ($customerCompany->getAddresses() as $address) {
            $customerCompany->removeAddress($address);
            $this->em->remove($address);
        }

        $this->em->flush();

        $this->em->remove($customerCompany);
        $this->em->flush();
    }

    /**
     * Supprime un utilisateur d'une société uniquement
     */
    public function removeAccount(CustomerCompany $customerCompany, CustomerAccount $customerAccount): void
    {
        if (!$customerCompany->getAccounts()->contains($customerAccount)) {
            return;
        }

        // on supprime le panier
        $cart = $this->cartManager->getCart($customerAccount);
        if ($cart) {
            foreach ($cart->getItems() as $item) {
                $this->em->remove($item);
                $cart->removeItem($item);
            }
            if ($cart->getOrder()) {
                $this->em->remove($cart->getOrder());
            }
            $this->em->remove($cart);
        }
        $this->em->flush();

        // on récupère son panier et les éléments du panier et on le supprime
        $orders = $this->cartManager->getOrders($customerAccount);
        if ($orders) {
            foreach ($orders as $order) {
                $this->em->remove($order);
            }
            $this->em->flush();
        }

        // on ne retire que la liaison avec le compte client
        $customerCompany->removeAccount($customerAccount);

        // si le compte a 1 seul compte rattaché alors on le supprime et tout ce qui est rattaché à ce compte
        if ($customerAccount->getUser()->getAccounts()->count() === 1) {
            // on supprime le compte Utilisateur
            $this->em->remove($customerAccount->getUser());
        }
        $customerAccount->setUser(null);
        $this->em->remove($customerAccount);
        $this->em->flush();
    }

    /**
     * Assigne le propriétaire de la société
     */
    public function setCompanyOwner(CustomerCompany $customerCompany, CustomerAccount $customerAccount): void
    {
        // Désactive le rôle d'owner sur l'ensemble des comptes sauf pour le nouveau propriétaire
        $customerCompany->getAccounts()->map(function (CustomerAccount $user) use ($customerAccount) {
            if ($user === $customerAccount) {
                $user->setOwner(true);
            } else {
                $user->setOwner(false);
            }
        });

        $this->em->flush();
    }

    /**
     * Retourne une entreprise selon un numéro de siret
     */
    public function isExistingSiret(string $siret): bool
    {
        if (empty($siret)) {
            return false;
        }

        return $this->em->getRepository(CustomerCompany::class)->findOneBy(['siret' => $siret]) !== null;
    }

    /**
     * Si la société de l'utilisateur possède un code SAGE,
     * effectue l'appel pour mettre à jour les informations dans sage BO
     */
    public function updateAccountInfoFromSageBO(CustomerCompany $company): ?string
    {
        return $this->sageManager->saveCustomerAccountOnSage($company);
    }

    /**
     * Sauvegarde d'une société
     */
    public function createOrSaveCompanyBo(CustomerCompany $customerCompany): bool
    {
        $isNewCompany = !$customerCompany->getId();
        if ($isNewCompany) {
            $this->em->persist($customerCompany);
        }

        // on met à jour la géolocalisation de l'adresse
        $geometry = $this->googleMapHandler->searchCustomerCompanyAddress(
            $customerCompany->getMainAddress()
        );
        if ($geometry) {
            $customerCompany->setLat($geometry['lat']);
            $customerCompany->setLon($geometry['lon']);
        }
        $this->sageLogger->info('GMAP Géolocalisation BO', [
            'data' => $geometry
        ]);

        // on met à jour ou crée l'utilisateur dans Sage
        $dataSage = $this->updateAccountInfoFromSageBO($customerCompany);
        if ($dataSage) {
            $customerCompany->setSageCode($dataSage);
        }
        $this->sageLogger->info('Update Sage BO', [
            'data' => $dataSage
        ]);

        try {
            $this->thotem->updateContacts(
                $customerCompany,
                $this->em->getRepository(CustomerAccount::class)->getOwnerForCompany($customerCompany)->getUser()
            );

            $thotemUpdated = true;
        } catch (\Throwable) {
            $thotemUpdated = false;
        }

        $this->em->flush();

        return $thotemUpdated;
    }
}
