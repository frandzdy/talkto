<?php

namespace App\Controller\Back;

use App\Entity\Product;
use App\Entity\Reservation;
use App\Entity\User;
use App\Exporter\LessorExporter;
use App\Form\Back\LessorFilterType;
use App\Repository\UserRepository;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\SwitchUserToken;

/**
 * Gestion des bailleurs.
 */
#[Route(path: '/lessors', name: 'lessor_')]
class LessorController extends AbstractController
{
    final public const LESSORS_PER_PAGE = 50;

    final public const LESSORS_TERM_FILTER = 'lessor.filter';

    /**
     * Liste des bailleurs.
     */
    #[Route(path: '/', name: 'index', methods: ['GET', 'POST'])]
    public function index(Request $request, UserRepository $userRepository, PaginatorInterface $paginator): Response
    {
        $filtersFormSession = $request->getSession()->get(self::LESSORS_TERM_FILTER, null);
        if (!$filtersFormSession) {
            $filters = [
                'term' => $request->query->get('term', ''),
                'status' => $request->query->get('status', 0),
            ];
        } else {
            $filters = $filtersFormSession;
        }

        $page = $request->query->getInt('page', 0) > 0 ? $request->query->getInt('page') : 1;

        $filterForm = $this->createForm(LessorFilterType::class, $filters);
        if ($filterForm->handleRequest($request)->isSubmitted() && $filterForm->isValid()) {
            $filters = $filterForm->getData() ?? [];
            $request->getSession()->set(self::LESSORS_TERM_FILTER, $filters);
        }

        $query = $userRepository->buildSearchQuery($filters, true);

        $paginator = $paginator->paginate(
            $query,
            $page,
            self::LESSORS_PER_PAGE,
            [
                PaginatorInterface::DEFAULT_SORT_FIELD_NAME => 'u.firstname',
                PaginatorInterface::DEFAULT_SORT_DIRECTION => 'ASC',
                PaginatorInterface::DISTINCT => false,
            ]
        );

        return $this->render(
            'back/lessor/index.html.twig',
            [
                'lessors' => $paginator,
                'filterForm' => $filterForm->createView(),
            ]
        );
    }

    /**
     * Affichage d'une fiche bailleur.
     */
    #[Route(path: '/{id<\d+>}', name: 'show', methods: ['GET', 'POST'])]
    public function show(User $lessor, EntityManagerInterface $em): Response
    {
        $reservations = $em->getRepository(Reservation::class)->findBy(['author' => $lessor->getId()]);
        $products = $em->getRepository(Product::class)->findBy(['author' => $lessor->getId()]);

        return $this->render('back/lessor/show.html.twig', [
            'lessor' => $lessor,
            'reservations' => $reservations,
            'products' => $products,
        ]);
    }

    /**
     * Supprime d'un bailleur.
     */
    #[Route(path: '/{id<\d+>}/remove', name: 'delete', methods: ['POST'])]
    public function delete(User $lessor, UserManager $userManager): RedirectResponse
    {
        $userManager->deleteCustomer($lessor);

        return $this->redirectToRoute('back_lessor_index');
    }

    /**
     * Génère un export de tous les bailleurs.
     */
    #[Route(path: '/extract-lessor/{typeFile}', name: 'extract', requirements: ['typeFile' => '(csv|xlsx)'], defaults: ['typeFile' => 'xlsx'], methods: ['GET'])]
    public function export(
        UserRepository $userRepository,
        string $typeFile,
        LessorExporter $lessorExporter
    ): NotFoundHttpException|Response {
        $lessors = $userRepository->findBy(['role' => User::ROLE_SELLER]);
        $callable = 'exportAs'.strtoupper($typeFile);

        if (is_callable($callable, true, $callableNameFunction)) {
            $result = $lessorExporter->{$callableNameFunction}($lessors);
        } else {
            throw $this->createNotFoundException('Exporter Method not found');
        }

        return new Response(
            $result['file'],
            200,
            [
                'Content-Type' => $result['contentType'].'; charset=windows-1251',
                'Content-Disposition' => 'attachment; filename="export_bailleurs.'.$typeFile.'"',
            ]
        );
    }

    /**
     * S'en connecter en tant que bailleur.
     */
    #[Route(path: '/{id<\d+>}/authenticate', name: 'authenticate', methods: ['GET'])]
    public function authenticate(Request $request, User $user, TokenStorageInterface $tokenStorage): RedirectResponse
    {
        $roles = $user->getRoles();
        $roles[] = 'ROLE_PREVIOUS_ADMIN';
        $roles[] = 'ROLE_ALLOWED_TO_SWITCH';

        $url = $this->generateUrl('back_lessor_show', ['id' => $user->getId()]);

        $switchToken = new SwitchUserToken($user, 'front', $roles, $tokenStorage->getToken(), $url);

        $sessionKey = '_security_front';
        $session = $request->getSession();
        $session->set($sessionKey, serialize($switchToken));

        return $this->redirectToRoute('front_user_account');
    }
}
