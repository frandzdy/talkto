<?php

namespace App\Controller\Back;

use App\Entity\Transaction;
use App\Entity\TransactionLine;
use App\Exporter\TransactionExporter;
use App\Form\Back\CancelTransactionLineType;
use App\Form\Back\TransactionFilterType;
use App\Repository\TransactionRepository;
use App\Service\MailerManager;
use App\Service\StripeManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Gestion des bailleurs.
 */
#[Route(path: '/transactions', name: 'transaction_')]
class TransactionController extends AbstractController
{
    final public const TRANSACTIONS_PER_PAGE = 50;

    final public const TRANSACTIONS_TERM_FILTER = 'transaction.filter';

    /**
     * Liste des bailleurs.
     */
    #[Route(path: '/', name: 'index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        TransactionRepository $transactionRepository,
        PaginatorInterface $paginator
    ): Response {
        $filtersFormSession = $request->getSession()->get(self::TRANSACTIONS_TERM_FILTER, null);
        $filters = $filtersFormSession ?: ['term' => $request->query->get('term', '')];

        $page = $request->query->getInt('page', 0) > 0 ? $request->query->getInt('page') : 1;

        $filterForm = $this->createForm(TransactionFilterType::class, $filters);
        if ($filterForm->handleRequest($request)->isSubmitted() && $filterForm->isValid()) {
            $filters = $filterForm->getData() ?? [];
            $request->getSession()->set(self::TRANSACTIONS_TERM_FILTER, $filters);
        }

        $query = $transactionRepository->buildSearchQuery($filters);

        $paginator = $paginator->paginate(
            $query,
            $page,
            self::TRANSACTIONS_PER_PAGE,
            [
                PaginatorInterface::DEFAULT_SORT_FIELD_NAME => 't.status',
                PaginatorInterface::DEFAULT_SORT_DIRECTION => 'ASC',
                PaginatorInterface::DISTINCT => false,
            ]
        );

        return $this->render(
            'back/transaction/index.html.twig',
            [
                'transactions' => $paginator,
                'filterForm' => $filterForm->createView(),
            ]
        );
    }

    /**
     * Affichage d'une transaction.
     */
    #[Route(path: '/{transaction<\d+>}', name: 'show', methods: ['GET', 'POST'])]
    public function show(Transaction $transaction): Response
    {
        return $this->render('back/transaction/show.html.twig', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * Génère un export de tous les bailleurs.
     */
    #[Route(path: '/extract-transaction/{typeFile}', name: 'extract', requirements: ['typeFile' => '(csv|xlsx)'], defaults: ['typeFile' => 'xlsx'], methods: ['GET'])]
    public function export(
        TransactionRepository $transactionRepository,
        string $typeFile,
        TransactionExporter $transactionExporter
    ): NotFoundHttpException|Response {
        $transactions = $transactionRepository->findAll();
        $callable = 'exportAs'.strtoupper($typeFile);

        if (is_callable($callable, true, $callableNameFunction)) {
            $result = $transactionExporter->{$callableNameFunction}($transactions);
        } else {
            throw $this->createNotFoundException('Exporter Method not found');
        }

        return new Response(
            $result['file'],
            200,
            [
                'Content-Type' => $result['contentType'].'; charset=windows-1251',
                'Content-Disposition' => 'attachment; filename="export_transactions.'.$typeFile.'"',
            ]
        );
    }

    /**
     * Remboursement d'une transaction partiel ou complète.
     */
    #[Route('/annulation/{transactionLine}', name: 'cancel')]
    public function cancel(
        TransactionLine $transactionLine,
        Request $request,
        StripeManager $stripeManager,
        EntityManagerInterface $em,
        MailerManager $mailerManager
    ): Response {
        $refund = [
            'amount' => null,
        ];
        $options = [
            'action' => $request->getUri(),
            'maxAmount' => $transactionLine->getAmountTtc() / 100,
        ];
        $form = $this->createForm(CancelTransactionLineType::class, $refund, $options);

        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $refund = $form->getData();
            $transactionLine->setCancelTransfertId(
                $stripeManager->cancelTranfert($transactionLine->getTransfertId(), $refund['amount'])->id
            )
                ->setCancelAmount($refund['amount'] * 100)
            ;
            $em->flush();
            $mailerManager->sendMailNotification(
                $transactionLine->getTransaction()->getAuthor()->getEmail(),
                'emails/refunds.html.twig',
                [
                    'transactionLine' => $transactionLine,
                    'user' => $transactionLine->getTransaction()->getAuthor(),
                ]
            );
            $mailerManager->sendMailNotification(
                $transactionLine->getProduct()->getAuthor()->getEmail(),
                'emails/lessor_refunds.html.twig',
                [
                    'transactionLine' => $transactionLine,
                    'user' => $transactionLine->getProduct()->getAuthor(),
                ]
            );
            $this->addFlash('success', 'Annulation effectué.');

            return $this->json(
                [
                    'success' => true,
                    'redirectUrl' => $this->generateUrl(
                        'back_transaction_show',
                        ['transaction' => $transactionLine->getTransaction()->getId()]
                    ),
                ]
            );
        }

        return $this->render('back/transaction/cancel.html.twig', ['form' => $form]);
    }

    #[Route('/caution/{transactionLine}', name: 'caution')]
    public function caution(
        TransactionLine $transactionLine,
        Request $request,
        StripeManager $stripeManager,
        EntityManagerInterface $em,
        MailerManager $mailerManager
    ): Response {
        $caution = [
            'amount' => null,
        ];
        $options = [
            'action' => $request->getUri(),
            'maxAmount' => $transactionLine->getProduct()->getCaution() / 100,
        ];
        $form = $this->createForm(CancelTransactionLineType::class, $caution, $options);
        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $caution = $form->getData();
            $stripeManager->caution($transactionLine, $caution['amount']);
            $transactionLine
                ->setCautionAmount($caution['amount'] * 100)
            ;
            $em->flush();

            $mailerManager->sendMailNotification(
                $transactionLine->getTransaction()->getAuthor()->getEmail(),
                'emails/caution.html.twig',
                [
                    'transactionLine' => $transactionLine,
                    'user' => $transactionLine->getTransaction()->getAuthor(),
                ]
            );
            $mailerManager->sendMailNotification(
                $transactionLine->getProduct()->getAuthor()->getEmail(),
                'emails/lessor_caution.html.twig',
                [
                    'transactionLine' => $transactionLine,
                    'user' => $transactionLine->getProduct()->getAuthor(),
                ]
            );
            $this->addFlash('success', 'Caution effectué.');

            return $this->json(
                [
                    'success' => true,
                    'redirectUrl' => $this->generateUrl(
                        'back_transaction_show',
                        ['transaction' => $transactionLine->getTransaction()->getId()]
                    ),
                ]
            );
        }

        return $this->render('back/transaction/cancel.html.twig', ['form' => $form]);
    }
}
