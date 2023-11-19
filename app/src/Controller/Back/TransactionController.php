<?php

namespace App\Controller\Back;

use App\Entity\Transaction;
use App\Exporter\ProductExporter;
use App\Exporter\TransactionExporter;
use App\Form\Back\ProductFilterType;
use App\Repository\ProductRepository;
use App\Repository\TransactionRepository;
use App\Service\CustomerCompanyManager;
use App\Service\MailerManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Gestion des bailleurs
 */
#[Route(path: '/transactions', name: 'transaction_')]
class TransactionController extends AbstractController
{
    public const TRANSACTIONS_PER_PAGE = 50;
    public const TRANSACTIONS_TERM_FILTER = 'transaction.filter';

    /**
     * Liste des bailleurs
     */
    #[Route(path: '/', name: 'index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        TransactionRepository $transactionRepository,
        PaginatorInterface $paginator
    ): Response {
        $filtersFormSession = $request->getSession()->get(self::TRANSACTIONS_TERM_FILTER, null);
        if (!$filtersFormSession) {
            $filters = ['term' => $request->query->get('term', '')];
        } else {
            $filters = $filtersFormSession;
        }
        $page = $request->query->getInt('page', 0) > 0 ? $request->query->getInt('page') : 1;

        $filterForm = $this->createForm(ProductFilterType::class, $filters);
        if ($filterForm->handleRequest($request)->isSubmitted() && $filterForm->isValid()) {
            $filters = $filterForm->getData() ?? [];
            $request->getSession()->set(self::TRANSACTIONS_TERM_FILTER, $filters);
        }

        $query = $transactionRepository->buildSearchQuery($filters, true);

        $paginator = $paginator->paginate(
            $query,
            $page,
            self::TRANSACTIONS_PER_PAGE,
            [
                PaginatorInterface::DEFAULT_SORT_FIELD_NAME => 't.reference',
                PaginatorInterface::DEFAULT_SORT_DIRECTION => 'ASC',
                PaginatorInterface::DISTINCT => false
            ]
        );

        return $this->render(
            'back/transaction/index.html.twig',
            [
                'transactions' => $paginator,
                'filterForm' => $filterForm->createView()
            ]
        );
    }

    /**
     * Affichage d'une fiche bailleur
     */
    #[Route(path: '/{id<\d+>}', name: 'show', methods: ['GET', 'POST'])]
    public function show(Transaction $transaction): Response {
        return $this->render('back/transaction/show.html.twig', [
            'transaction' => $transaction
        ]);
    }

    /**
     * Supprime d'un bailleur
     */
    #[Route(path: '/{id<\d+>}/remove', name: 'delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Transaction $transaction, CustomerCompanyManager $customerCompanyManager): RedirectResponse
    {
        try {
            $customerCompanyManager->removeCompany($transaction);
        } catch (Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('back_transaction_index');
    }

    /**
     * Génère un export de tous les bailleurs
     */
    #[Route(path: '/extract-transaction/{typeFile}', name: 'extract', requirements: ['typeFile' => '(csv|xlsx)'], defaults: ['typeFile' => 'xlsx'], methods: ['GET'])]
    public function export(
        TransactionRepository $transactionRepository,
        string $typeFile,
        TransactionExporter $transactionExporter
    ): NotFoundHttpException|Response {
        $products = $transactionRepository->findAll();
        $callable = 'exportAs' . strtoupper($typeFile);

        if (is_callable($callable, true, $callableNameFunction)) {
            $result = $transactionExporter->$callableNameFunction($products);
        } else {
            throw $this->createNotFoundException('Exporter Method not found');
        }

        return new Response(
            $result['file'],
            200,
            [
                'Content-Type' => $result['contentType'] . '; charset=windows-1251',
                'Content-Disposition' => 'attachment; filename="export_transactions.' . $typeFile . '"'
            ]
        );
    }
}
