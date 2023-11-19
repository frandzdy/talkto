<?php

namespace App\Service;

use App\Entity\Civility;
use App\Entity\Country;
use App\Entity\CustomerAccount;
use App\Entity\CustomerBasket;
use App\Entity\CustomerCompany;
use App\Entity\LegalForm;
use App\Entity\RefDossierStatus;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Gestionnaire d'un compte client
 */
class CustomerAccountManager
{
    /**
     * constructor.
     */
    public function __construct(
        private EntityManagerInterface $em,
        private SageManager $sageManager,
        private \App\Service\Api\Thotem $thotem,
        private ApiGouvHandler $apiGouv
    ) {
    }

    /**
     * Initialise un compte client au status en attente de validation
     */
    public function initializeCustomerAccountForUser(User $user, CustomerCompany $customerCompany): CustomerAccount
    {
        return (new CustomerAccount())
            ->setUser($user)
            ->setCompany($customerCompany)
            ->setStatus(CustomerAccount::STATUS_PENDING)
            ->setOwner(false);
    }

    /**
     * Complète un compte utilisateur avec les données fournies et des données par défaut
     */
    public function initializeOwnerCustomerAccountForUser(CustomerCompany $customerCompany, User $user): CustomerAccount
    {
        return (new CustomerAccount())
            ->setCompany($customerCompany)
            ->setUser($user)
            ->setStatus(CustomerAccount::STATUS_PENDING)
            ->setOwner(true);
    }

    /**
     * Sauvegarde une entreprise ou la crée si elle n'existe pas
     * Mise à jour dans Sage et Mise à jour dans wordpress
     */
    public function saveOrEditCustomerAccount(
        CustomerAccount $customerAccount,
        bool $updateSage = true,
        bool $updateThotem = false
    ): void {
        if (!$customerAccount->getId()) {
            $this->em->persist($customerAccount);
        }
        if (!$customerAccount->getCompany()->getSageCode()) {
            // on récupère le code sage reçu par l'api
            $sageCode = $this->sageManager->getCustomerCodeBySiret($customerAccount->getCompany()->getSiret());
            if ($sageCode) {
                $customerAccount->getCompany()->setSageCode($sageCode['sageCode']);
            }
        }

        if ($updateSage) {
            // on met à jour les informations du client dans Sage il a un code sage.
            $this->updateAccountInfoFromSageFO($customerAccount);
        }
        if ($updateThotem) {
            $this->thotem->updateContacts($customerAccount->getCompany(), $customerAccount->getUser());
        }

        $this->em->flush();
    }

    /**
     * Supprime un compte lors du refus de l'ajout par le client
     */
    public function removeCustomerAccount(CustomerAccount $account): void
    {
        $baskets = $this->em->getRepository(CustomerBasket::class)->findBy(['account' => $account->getId()]);
        foreach ($baskets as $basket) {
            $this->em->remove($basket);
        }
        if ($account->getUser()->getAccounts()->count() == 1) {
            $this->em->remove($account->getUser());
        }
        $this->em->remove($account);

        $this->em->flush();
    }

    /**
     * Génère un token pour l'invitation d'ajout à un compte société
     */
    public function generateInvitationForAccount(CustomerAccount $account, string $format = '+1 hour'): void
    {
        $account->setInvitationToken(bin2hex(random_bytes(16)));
        $account->setInvitationTokenExpiredAt((new \DateTime($format)));
    }

    /**
     * supprime un token après sa validation
     */
    public function emptyInvitationForAccount(CustomerAccount $account): void
    {
        $account->setInvitationToken(null);
        $account->setInvitationTokenExpiredAt(null);
    }

    /**
     * Si la société de l'utilisateur ne possède pas de code SAGE,
     * effectue l'appel pour le récupérer via son siret
     */
    public function updateAccountCodeFromSage(CustomerAccount $account, bool $updateFromGouv = true): array
    {
        // si on n'a pas de code sage, on remplie les informations de l'entreprise
        if (!$account->getCompany()->getSageCode()) {
            // on appelle sage afin de récupérer les informations de l'entreprise avec le siret
            $dataSage = $this->sageManager->getCustomerCodeBySiret($account->getCompany()->getSiret());

            if ($dataSage) {
                $account->getCompany()
                    ->setSageCode($dataSage['sageCode'])
                    ->setLeaderCivility($dataSage['leaderCivility'] == 0 ? Civility::CIVILITY_MEN : Civility::CIVILITY_WOMEN)
                    ->setWebsite($dataSage['website'] ?? '')
                    ->setBillingEmail($dataSage['billingEmail'])
                    ->setCertificateEmail($dataSage['certificateEmail'])
                    ->setLat($dataSage['lat'])
                    ->setLon($dataSage['lon']);

                $account->getUser()
                    ->setPhoneNumber($dataSage['ownerPhoneNumber']);
            }

            if (!$updateFromGouv) {
                return $dataSage ?? [];
            }

            $dataGouv = $this->apiGouv->getAllDataForSiret($account->getCompany()->getSiret());

            // Maj des infos sage pour le blocage des champs dans le formulaire
            $dataSage['companyName'] = $dataGouv['etablissement']['name'] ?? '';
            $dataSage['legalForm'] = $dataGouv['entreprise']['legalForm'] ?? '';
            $dataSage['vatIdentifier'] = $dataGouv['entreprise']['vat'] ?? '';
            $dataSage['naf'] = $dataGouv['etablissement']['naf'] ?? '';
            $dataSage['companyFoundedDate'] = $dataGouv['entreprise']['companyFoundedDate'] ?? null;
            $dataSage['leaderLastName'] = $dataGouv['leader']['lastName'] ?? '';
            $dataSage['leaderFirstName'] = $dataGouv['leader']['firstName'] ?? '';

            $dataSage['mainAddress']['address'] = $dataGouv['etablissement']['address']['address'] ?? '';
            $dataSage['mainAddress']['addressAdditional'] = $dataGouv['etablissement']['address']['addressAdditional'] ?? '';
            $dataSage['mainAddress']['zipCode'] = $dataGouv['etablissement']['address']['zipcode'] ?? '';
            $dataSage['mainAddress']['city'] = $dataGouv['etablissement']['address']['city'] ?? '';
            $dataSage['mainAddress']['country'] = $dataGouv['etablissement']['address']['country'] ?? '';

            $account->getCompany()
                ->setLegalForm($this->em->getRepository(LegalForm::class)->findOneBy(['label' => $dataGouv['entreprise']['legalForm']]))
                ->setCompanyName($dataGouv['etablissement']['name'])
                ->setVatIdentifier($dataGouv['entreprise']['vat'])
                ->setNaf($dataGouv['etablissement']['naf'])
                ->setCompanyFoundedDate($dataSage['companyFoundedDate'] ? \DateTimeImmutable::createFromFormat('Y-m-d', $dataSage['companyFoundedDate']) : null)
                ->setLeaderLastname($dataGouv['leader']['lastName'] ?? '')
                ->setLeaderFirstname($dataGouv['leader']['firstName'] ?? '');

            $account->getCompany()->getMainAddress()
                ->setAddress($dataGouv['etablissement']['address']['address'])
                ->setAddressAdditional($dataGouv['etablissement']['address']['addressAdditional'])
                ->setZipcode($dataGouv['etablissement']['address']['zipcode'])
                ->setCity($dataGouv['etablissement']['address']['city'])
                ->setCountry($this->em->getRepository(Country::class)->findOneBy(['label' => $dataGouv['etablissement']['address']['country']]))
            ;

            return $dataSage;
        }

        return [];
    }

    /**
     * Si la société de l'utilisateur possède un code SAGE,
     * effectue l'appel pour mettre à jour les informations dans sage FO
     */
    public function updateAccountInfoFromSageFO(CustomerAccount $account): void
    {
        if ($account->getCompany()->getSageCode()) {
            // Si aucun account on définit le user comme owner
            if (count($account->getCompany()->getAccounts()) === 0) {
                $account->setOwner(true);
                $account->getCompany()->addAccount($account);
            }
            $this->sageManager->saveCustomerAccountOnSage($account->getCompany());
        }
    }

    /**
     * Modifie le statut d'un utilisateur de la société
     */
    public function toggleCustomerAccountStatus(CustomerAccount $account): void
    {
        $account->setStatus(($account->getStatus() == CustomerAccount::STATUS_PENDING) ? CustomerAccount::STATUS_ACTIVE : CustomerAccount::STATUS_PENDING);

        $valid = false;
        foreach ($account->getUser()->getAccounts() as $userAccount) {
            $valid = $valid || ($userAccount->getStatus() === CustomerAccount::STATUS_ACTIVE);
        }
        $account->getUser()->setStatus($valid ? User::STATUS_VALIDATED : User::STATUS_PENDING_VALIDATION);
        $this->em->flush();
    }

    /**
     * Actions à faire sur les sirets en parametre
     */
    public function getRemainingActions(User $user, array $accounts): array
    {
        $actions = [];
        $accountIds = [];

        foreach ($accounts as $account) {
            $accountIds[] = $account->getId();
        }

        $rep = $this->em->getRepository(CustomerAccount::class);

        // Statut des dossiers
        $limitDateInfos = $rep->hasDossierNearLimitDate($accountIds);
        foreach ($rep->countRemainingActionsOnDossierByAccounts($accountIds) as $id => $data) {
            if ($data['suspended'] ?? 0) {
                $actions[$id]['important']['suspended'] = $data['suspended'];
            }

            if ($data['status_' . RefDossierStatus::COMMITTEE_ADJOURNED] ?? 0) {
                $actions[$id]['important']['ajourned'] = $data['status_' . RefDossierStatus::COMMITTEE_ADJOURNED];
            }

            if ($data['status_' . RefDossierStatus::INVOICED] ?? 0) {
                $actions[$id]['important']['invoiced'] = $data['status_' . RefDossierStatus::INVOICED];
            }

            if ($data['status_' . RefDossierStatus::TO_COMPLETE] ?? 0) {
                $actions[$id][isset($limitDateInfos[$id]) ? 'important' : 'normal']['toComplete'] = $data['status_' . RefDossierStatus::TO_COMPLETE];
            }

            if ($data['status_' . RefDossierStatus::TO_FULFILL] ?? 0) {
                $actions[$id]['normal']['toFulfill'] = $data['status_' . RefDossierStatus::TO_FULFILL];
            }
        }

        // Chantier(s) à compléter
        foreach ($rep->countToCompleteWorksiteForAccounts($accountIds) as $id => $data) {
            if ($data["countToCompleteWorksite"] ?? 0) {
                $actions[$id]['normal']['worksite'] = $data["countToCompleteWorksite"];
            }
        }

        // Demande(s) d’invitation
        foreach ($rep->countInvitationForAccounts($accountIds) as $id => $data) {
            if ($data["countInvitation"] ?? 0) {
                $actions[$id]['normal']['invitation'] = $data["countInvitation"];
            }
        }

        return $actions;
    }
}
