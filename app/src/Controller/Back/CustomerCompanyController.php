<?php

namespace App\Controller\Back;

use App\Entity\CustomerAccount;
use App\Entity\CustomerCompany;
use App\Entity\User;
use App\Exporter\CustomerCompanyExporter;
use App\Form\Type\Back\CompanyType;
use App\Form\Type\Back\CustomerAccountCreationBoType;
use App\Form\Type\Back\CustomerAccountFilterType;
use App\Form\Type\Back\UserCreationModalType;
use App\Form\Type\Back\UserType;
use App\Repository\CertificateRepository;
use App\Repository\CustomerAccountRepository;
use App\Repository\CustomerCompanyRepository;
use App\Repository\UserRepository;
use App\Service\Api\Thotem;
use App\Service\ApiGouvHandler;
use App\Service\CustomerAccountManager;
use App\Service\CustomerCompanyManager;
use App\Service\SageManager;
use App\Service\ThotemDossierManager;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\SwitchUserToken;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Class CompanyController
 * @package App\Controller\Back
 */
#[Route(path: '/company-customers', name: 'company_')]
class CustomerCompanyController extends AbstractController
{
    public const COMPANYS_PER_PAGE = 50;
    public const COMPANYS_TERM_FILTER = 'company.filter';
    public const COMPANY_SIRET = 'company.siret';
    public const COMPANY_ADDRESS = 'company.companyAddress';

    /**
     * Liste des sociétés clients
     */
    #[Route(path: '/', name: 'index', methods: ['GET', 'POST'])]
    public function index(Request $request, CustomerCompanyRepository $customerCompanyRepository, PaginatorInterface $paginator): Response
    {
        $filtersFormSession = $request->getSession()->get(self::COMPANYS_TERM_FILTER, null);
        if (!$filtersFormSession) {
            $filters = ['term' => $request->query->get('term', '')];
        } else {
            $filters = $filtersFormSession;
        }
        $page = $request->query->getInt('page', 0) > 0 ? $request->query->getInt('page') : 1;

        $filterForm = $this->createForm(CustomerAccountFilterType::class, $filters);
        if ($filterForm->handleRequest($request)->isSubmitted() && $filterForm->isValid()) {
            $filters = $filterForm->getData() ?? [];
            $request->getSession()->set(self::COMPANYS_TERM_FILTER, $filters);
        }

        $query = $customerCompanyRepository->buildSearchQuery($filters);

        $paginator = $paginator->paginate(
            $query,
            $page,
            self::COMPANYS_PER_PAGE,
            [PaginatorInterface::DEFAULT_SORT_FIELD_NAME => 'c.companyName', PaginatorInterface::DEFAULT_SORT_DIRECTION => 'ASC', PaginatorInterface::DISTINCT => false]
        );

        return $this->render(
            'back/company/index.html.twig',
            [
                'companys' => $paginator,
                'filterForm' => $filterForm->createView()
            ]
        );
    }

    /**
     * Modification d'une fiche client
     */
    #[Route(path: '/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        CustomerCompany $customerCompany,
        CustomerCompanyManager $customerCompanyManager,
        CertificateRepository $certificateRepository
    ): Response {
        $request->getSession()->set(self::COMPANY_SIRET, $customerCompany->getSiret());
        if ($customerCompany->getMainAddress()) {
            $request->getSession()->set(self::COMPANY_ADDRESS, clone $customerCompany->getMainAddress());
        }

        $form = $this->createForm(CompanyType::class, $customerCompany, ['company' => $customerCompany]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $success = $customerCompanyManager->createOrSaveCompany($customerCompany);
            if ($success) {
                $this->addFlash('success', 'Modifications enregistrées avec succès');
            } else {
                $this->addFlash('error', "Erreur lors de l'enregistrement dans Sage");
            }

            return $this->redirectToRoute('back_company_edit', ['id' => $customerCompany->getId()]);
        }

        $validCertificates = $certificateRepository->getAllValidCertificatesForDownload($customerCompany);

        return $this->render('back/company/edit.html.twig', [
            'form' => $form->createView(),
            'customerCompany' => $customerCompany,
            'hasValidCertificates' => count($validCertificates) > 0,
        ]);
    }

    /**
     * Création d'une fiche client
     */
    #[Route(path: '/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        CustomerAccountManager $customerAccountManager,
        CustomerCompanyManager $customerCompanyManager,
        UserManager $userManager,
        CustomerAccountRepository $customerAccountRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $userManager->initializeUserPendingValidation();
        $customerCompany = $customerCompanyManager->initializeCustomerCompany(null, null);
        $customerAccount = $customerAccountManager->initializeOwnerCustomerAccountForUser($customerCompany, $user);
        $user->setStatus(User::STATUS_VALIDATED);
        $customerAccount->setStatus(CustomerAccount::STATUS_ACTIVE);

        $request->getSession()->set(self::COMPANY_SIRET, $customerAccount->getCompany()->getSiret() ?? 1);
        if ($customerCompany->getMainAddress()) {
            $request->getSession()->set(self::COMPANY_ADDRESS, clone $customerCompany->getMainAddress());
        }

        $form = $this->createForm(CustomerAccountCreationBoType::class, $customerAccount, [
            'company' => $customerCompany
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->get('user')->getData();
            $inviteAccounts = $customerAccount->getCompany()->getInviteAccounts();
            $company = $customerAccount->getCompany();
            if (!count($inviteAccounts)) {
                if ($user->getPlainPassword()) {
                    $userManager->changeUserPassword($customerAccount->getUser(), $form->get('user')->get('plainPassword')->getData());
                } else {
                    $user->setPassword('');
                }
                $userManager->saveUser($customerAccount->getUser());
                $customerAccount = $customerAccountManager->initializeOwnerCustomerAccountForUser($company, $customerAccount->getUser());
                $customerAccount->setStatus(CustomerAccount::STATUS_ACTIVE);
                $customerAccount->getUser()->setStatus(User::STATUS_VALIDATED);
                $entityManager->persist($customerAccount);
                $customerAccountManager->saveOrEditCustomerAccount($customerAccount);
            } else {
                // on traite les comptes invités
                foreach ($inviteAccounts as $inviteAccount) {
                    $newCustomerAccount = $customerAccountManager->initializeCustomerAccountForUser($inviteAccount, $company);
                    $newCustomerAccount->setStatus(CustomerAccount::STATUS_ACTIVE);
                    $newCustomerAccount->getUser()->setStatus(User::STATUS_VALIDATED);
                    // si ce compte est propriétaire alors on l'ajout en tant que
                    $newCustomerAccount->setOwner($customerAccountRepository->isOwnerOfACompany($inviteAccount));
                    $entityManager->persist($newCustomerAccount);
                    $customerAccountManager->saveOrEditCustomerAccount($newCustomerAccount);
                }
            }

            $entityManager->flush();
            $thotemUpdated = $customerCompanyManager->createOrSaveCompanyBo($company);

            if (!$thotemUpdated) {
                $this->addFlash('error', 'Une erreur est survenue lors de la création du client dans Thotem.');
            }

            return $this->redirectToRoute('back_company_edit', ['id' => $customerAccount->getCompany()->getId()]);
        }

        return $this->render(
            'back/customerAccount/edit.html.twig',
            [
                'form' => $form->createView(),
                'customerCompany' => $customerAccount->getCompany()
            ]
        );
    }

    /**
     * Récupère les informations sage pour un siret
     */
    #[Route(path: '/sage-info/{siret}', name: 'sage_info', options: ['expose' => true], methods: ['GET'])]
    public function getSageData(string $siret, SageManager $sageManager, ApiGouvHandler $apiGouv): JsonResponse
    {
        $sageData = $sageManager->getCustomerCodeBySiret($siret);
        return new JsonResponse([
            'result' => $sageData !== null,
            'sageData' => $sageData,
            'gouvData' => $apiGouv->getAllDataForSiret($siret)
        ]);
    }

    /**
     * Edition d'un utilisateur lié à une société
     */
    #[Route(path: '/{user}/edit-user/{company}/company', name: 'edit_user_company', methods: ['GET', 'POST'])]
    public function editCompanyUser(
        Request $request,
        User $user,
        CustomerCompany $company,
        UserManager $userManager,
        Thotem $thotem
    ): Response {
        $form = $this->createForm(UserType::class, $user, [
            'validation_groups' => 'boEdition',
            'action' => $this->generateUrl('back_company_edit_user_company', [
                'user' => $user->getId(),
                'company' => $company->getId()
            ]),
        ]);

        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            if ($form->get('plainPassword')->getData()) {
                $userManager->changeUserPassword($user, $form->get('plainPassword')->getData());
            }
            $userManager->saveUser($user);

            $thotem->updateContacts($company, $user);

            $this->addFlash('success', "Modifications enregistrées avec succès");

            return $this->json(['success' => true, 'redirectUrl' => $this->generateUrl('back_company_index')]);
        }

        return $this->render('back/company/edit_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Création d'un utilisateur lié à une société
     */
    #[Route(path: '/create-user/{company}/company', name: 'create_user_company', methods: ['GET', 'POST'])]
    public function createCompanyUser(
        Request $request,
        CustomerCompany $company,
        UserManager $userManager,
        CustomerAccountManager $customerAccountManager,
        CustomerCompanyRepository $customerCompanyRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $userManager->initializeUser();

        $form = $this->createForm(UserCreationModalType::class, $user, [
            'validation_groups' => 'boCreation',
            'action' => $this->generateUrl('back_company_create_user_company', [
                'company' => $company->getId(),
            ]),
        ]);

        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            // On n'a pas de compte invité alors on finalise la création du compte user
            if (!count($form->get('inviteAccounts')->getData())) {
                if ($form->get('plainPassword')->getData()) {
                    $userManager->changeUserPassword($user, $form->get('plainPassword')->getData());
                } else {
                    $user->setPassword('');
                }
                $userManager->saveUser($user);
                $customerAccount = $customerAccountManager->initializeCustomerAccountForUser($user, $company);
                $customerAccount->setStatus(CustomerAccount::STATUS_ACTIVE);
                $customerAccount->getUser()->setStatus(User::STATUS_VALIDATED);
                $entityManager->persist($customerAccount);
                $customerAccountManager->saveOrEditCustomerAccount($customerAccount, false, true);
            } else {
                // sinon on crée un compte pour chaque compte invité pour l'entreprise
                foreach ($form->get('inviteAccounts')->getData() as $inviteAccount) {
                    if (!$customerCompanyRepository->isAllReadyInCompany($company, $inviteAccount)) {
                        // on ajout le compte à l'entreprise
                        $customerAccount = $customerAccountManager->initializeCustomerAccountForUser($inviteAccount, $company);
                        $customerAccount->setStatus(CustomerAccount::STATUS_ACTIVE);
                        $customerAccount->getUser()->setStatus(User::STATUS_VALIDATED);
                        $entityManager->persist($customerAccount);
                        $customerAccountManager->saveOrEditCustomerAccount($customerAccount, false, true);
                    }
                }
            }

            $entityManager->flush();

            $this->addFlash('success', "Création enregistrées avec succès");

            return $this->json(['success' => true, 'redirectUrl' => $this->generateUrl('back_company_index')]);
        }

        return $this->render('back/company/create_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Supprime l'entreprise
     */
    #[Route(path: '/company/{id}/remove', name: 'delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(CustomerCompany $customerCompany, CustomerCompanyManager $customerCompanyManager): RedirectResponse
    {
        try {
            $customerCompanyManager->removeCompany($customerCompany);
        } catch (Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('back_company_index');
    }

    /**
     * Suppression d'un utilisateur lié à une société
     */
    #[Route(path: '/{customerCompany}/accounts/{customerAccount}/remove', name: 'delete_user_company', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteCompanyUser(CustomerCompany $customerCompany, CustomerAccount $customerAccount, CustomerCompanyManager $customerCompanyManager): RedirectResponse
    {
        try {
            if (!$customerAccount->getOwner()) {
                $customerCompanyManager->removeAccount($customerCompany, $customerAccount);
            } else {
                $this->addFlash('error', 'Vous ne pouvez pas supprimer ce compte car il est propriétaire');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('back_company_edit', ['id' => $customerCompany->getId()]);
    }

    /**
     * Change le statut d'un utilisateur lié à une société
     */
    #[Route(path: '/{id}/change-status-user', name: 'change_user_status', options: ['expose' => true], methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function changeStatusUser(CustomerAccount $customerAccount, CustomerAccountManager $customerAccountManager): JsonResponse
    {
        try {
            $customerAccountManager->toggleCustomerAccountStatus($customerAccount);
            return new JsonResponse(['success' => true], JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            return new JsonResponse(['success' => false], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Change le propriétaire de la société
     */
    #[Route(path: '/{customerCompany}/{customerAccount}/change-owner-user', name: 'change_company_owner', options: ['expose' => true], methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function changeOwnerUser(CustomerCompany $customerCompany, CustomerAccount $customerAccount, CustomerCompanyManager $customerCompanyManager): JsonResponse
    {
        try {
            $customerCompanyManager->setCompanyOwner($customerCompany, $customerAccount);

            return new JsonResponse(true, JsonResponse::HTTP_OK);
        } catch (Exception $e) {
            return new JsonResponse(false, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Génère un export de toutes les entreprises et de ses utilisateurs rattachés
     */
    #[Route(path: '/extract-company/{typeFile}', name: 'extract_company', requirements: ['typeFile' => '(csv|xlsx)'], defaults: ['typeFile' => 'xlsx'], methods: ['GET'])]
    public function export(CustomerAccountRepository $customerAccountRepository, string $typeFile, CustomerCompanyExporter $customerCompanyExporter): NotFoundHttpException|Response
    {
        $customerAccounts = $customerAccountRepository->getAllCustomerAccount();
        $callable = 'exportAs' . strtoupper($typeFile);
        if (is_callable($callable, true, $callableNameFunction)) {
            $result = $customerCompanyExporter->$callableNameFunction($customerAccounts);
        } else {
            throw $this->createNotFoundException('Exporter Method not found');
        }

        return new Response(
            $result['file'],
            200,
            [
                'Content-Type' => $result['contentType'] . '; charset=windows-1251',
                'Content-Disposition' => 'attachment; filename="export_clients.' . $typeFile . '"'
            ]
        );
    }

    /**
     * Retourne la liste auto-complété des utilisateurs
     */
    #[Route(path: '/autocomplete-users/{ids}', name: 'autocomplete_users', options: ['expose' => true], defaults: ['ids' => ''], methods: ['GET'])]
    public function autocompleteUsers(
        string $ids,
        Request $request,
        UserRepository $userRepository,
        CustomerAccountRepository $customerAccountRepository
    ): JsonResponse {
        $users = $userRepository->getUserByTerm(
            $request->query->get('term', ''),
            explode('-', $ids)
        );

        $res = [];
        foreach ($users as $user) {
            $user->setIsOwnerOfCompany($customerAccountRepository->isOwnerOfACompany($user));
            $res[] = [
                'value' => $user->getFullName(),
                'isOwnerOfACompany' => $user->isOwnerOfCompany(),
                'html' => $this->renderView('back/customerAccount/_user.html.twig', ['user' => $user])
            ];
        }

        return new JsonResponse($res);
    }

    #[Route(path: '/{id}/authenticate', name: 'authenticate', methods: ['GET'])]
    public function authenticate(Request $request, CustomerCompany $customerCompany, TokenStorageInterface $tokenStorage): RedirectResponse
    {
        $user = $customerCompany->getOwner();
        $roles = $user->getRoles();
        $roles[] = 'ROLE_PREVIOUS_ADMIN';

        $url = $this->generateUrl('back_company_edit', ['id' => $customerCompany->getId()]);

        $switchToken = new SwitchUserToken($user, 'front', $roles, $tokenStorage->getToken(), $url);

        $sessionKey = '_security_front';
        $session = $request->getSession();
        $session->set($sessionKey, serialize($switchToken));

        return $this->redirectToRoute('front_customer_dashboard');
    }

    #[Route(path: '/{id}/sync-thotem', name: 'sync_thotem', methods: ['GET'])]
    public function syncThotem(CustomerCompany $customerCompany, ThotemDossierManager $thotem): RedirectResponse
    {
        try {
            $thotem->processCustomerCompany($customerCompany->getSageCode());
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la synchronisation avec Thotem');
        }

        $this->addFlash('success', 'Synchronisation avec Thotem terminée avec succès');

        return $this->redirectToRoute('back_company_edit', ['id' => $customerCompany->getId()]);
    }
}
