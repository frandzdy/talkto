<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Exporter\CustomerExporter;
use App\Form\Back\UserFilterType;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use App\Service\UserManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\SwitchUserToken;

/**
 * Class CompanyController.
 */
#[Route(path: '/customers', name: 'customer_')]
class CustomerController extends AbstractController
{
    final public const CUSTOMERS_PER_PAGE = 50;

    final public const CUSTOMERS_TERM_FILTER = 'user.filter';

    /**
     * Liste des sociétés clients.
     */
    #[Route(path: '/', name: 'index', methods: ['GET', 'POST'])]
    public function index(Request $request, UserRepository $userRepository, PaginatorInterface $paginator): Response
    {
        $filtersFormSession = $request->getSession()->get(self::CUSTOMERS_TERM_FILTER, null);
        $filters = $filtersFormSession ?: ['term' => $request->query->get('term', '')];

        $page = $request->query->getInt('page', 0) > 0 ? $request->query->getInt('page') : 1;

        $filterForm = $this->createForm(UserFilterType::class, $filters);
        if ($filterForm->handleRequest($request)->isSubmitted() && $filterForm->isValid()) {
            $filters = $filterForm->getData() ?? [];
            $request->getSession()->set(self::CUSTOMERS_TERM_FILTER, $filters);
        }

        $query = $userRepository->buildSearchQuery($filters);

        $paginator = $paginator->paginate(
            $query,
            $page,
            self::CUSTOMERS_PER_PAGE,
            [
                PaginatorInterface::DEFAULT_SORT_FIELD_NAME => 'u.firstname',
                PaginatorInterface::DEFAULT_SORT_DIRECTION => 'ASC',
                PaginatorInterface::DISTINCT => false,
            ]
        );

        return $this->render(
            'back/customer/index.html.twig',
            [
                'renters' => $paginator,
                'filterForm' => $filterForm->createView(),
            ]
        );
    }

    /**
     * Modification d'une fiche client.
     */
    #[Route(path: '/{id<\d+>}', name: 'show', methods: ['GET', 'POST'])]
    public function show(
        User $customer,
        ReservationRepository $reservationRepository
    ): Response {
        $reservations = $reservationRepository->findBy(['author' => $customer->getId()]);

        return $this->render('back/customer/show.html.twig', [
            'customer' => $customer,
            'reservations' => $reservations,
        ]);
    }

    /**
     * Supprime le client.
     */
    #[Route(path: '/{id<\d+>}/remove', name: 'delete', methods: ['POST'])]
    public function delete(User $customer, UserManager $userManager): RedirectResponse
    {
        $userManager->deleteCustomer($customer);

        return $this->redirectToRoute('back_customer_index');
    }

    /**
     * Génère un export de toutes les entreprises et de ses utilisateurs rattachés.
     */
    #[Route(path: '/extract-customer/{typeFile}', name: 'extract', requirements: ['typeFile' => '(csv|xlsx)'], defaults: ['typeFile' => 'xlsx'], methods: ['GET'])]
    public function export(
        UserRepository $userRepository,
        string $typeFile,
        CustomerExporter $customerExporter
    ): NotFoundHttpException|Response {
        $customers = $userRepository->findBy(['role' => [User::ROLE_USER, User::ROLE_GUESS]]);
        $callable = 'exportAs'.strtoupper($typeFile);
        if (is_callable($callable, true, $callableNameFunction)) {
            $result = $customerExporter->{$callableNameFunction}($customers);
        } else {
            throw $this->createNotFoundException('Exporter Method not found');
        }

        return new Response(
            $result['file'],
            200,
            [
                'Content-Type' => $result['contentType'].'; charset=windows-1251',
                'Content-Disposition' => 'attachment; filename="export_clients.'.$typeFile.'"',
            ]
        );
    }

    #[Route(path: '/{id<\d+>}/authenticate', name: 'authenticate', methods: ['GET'])]
    public function authenticate(SessionInterface $session, User $user, TokenStorageInterface $tokenStorage): RedirectResponse
    {
        $roles = $user->getRoles();
        $roles[] = 'ROLE_PREVIOUS_ADMIN';
        $roles[] = 'ROLE_ALLOWED_TO_SWITCH';

        $url = $this->generateUrl('back_customer_show', ['id' => $user->getId()]);

        $switchToken = new SwitchUserToken($user, 'front', $roles, $tokenStorage->getToken(), $url);

        $sessionKey = '_security_front';
        $session->set($sessionKey, serialize($switchToken));

        return $this->redirectToRoute('front_user_account');
    }
}
