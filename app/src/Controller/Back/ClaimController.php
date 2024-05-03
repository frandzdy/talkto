<?php

namespace App\Controller\Back;

use App\Entity\Claim;
use App\Entity\TransactionLine;
use App\Enum\ClaimStatus;
use App\Exporter\TransactionExporter;
use App\Form\Back\CancelTransactionLineType;
use App\Form\Back\ClaimFilterType;
use App\Repository\ClaimRepository;
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
 * Gestion des réclamations.
 */
#[Route(path: '/reclamation', name: 'claim_')]
class ClaimController extends AbstractController
{
    final public const CLAIM_PER_PAGE = 50;

    final public const CLAIM_TERM_FILTER = 'claim.filter';

    /**
     * Liste des réclamations.
     */
    #[Route(path: '/', name: 'index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        ClaimRepository $claimRepository,
        PaginatorInterface $paginator
    ): Response {
        $filtersFormSession = $request->getSession()->get(self::CLAIM_TERM_FILTER, null);
        if (!$filtersFormSession) {
            $filters = [
                'term' => $request->query->get('term', ''),
                'status' => $request->query->getEnum('status', ClaimStatus::class, ClaimStatus::PENDING),
            ];
        } else {
            $filters = $filtersFormSession;
        }

        $page = $request->query->getInt('page', 0) > 0 ? $request->query->getInt('page') : 1;

        $filterForm = $this->createForm(ClaimFilterType::class, $filters);
        if ($filterForm->handleRequest($request)->isSubmitted() && $filterForm->isValid()) {
            $filters = $filterForm->getData() ?? [];
            $request->getSession()->set(self::CLAIM_TERM_FILTER, $filters);
        }

        $query = $claimRepository->buildSearchQuery($filters);

        $paginator = $paginator->paginate(
            $query,
            $page,
            self::CLAIM_PER_PAGE,
            [
                PaginatorInterface::DEFAULT_SORT_FIELD_NAME => 'c.status',
                PaginatorInterface::DEFAULT_SORT_DIRECTION => 'ASC',
                PaginatorInterface::DISTINCT => false,
            ]
        );

        return $this->render(
            'back/claim/index.html.twig',
            [
                'claims' => $paginator,
                'filterForm' => $filterForm->createView(),
            ]
        );
    }

    /**
     * Affichage d'une réclamation.
     */
    #[Route(path: '/{claim<\d+>}', name: 'show', methods: ['GET', 'POST'])]
    public function show(Claim $claim): Response
    {
        return $this->render('back/claim/show.html.twig', [
            'claim' => $claim,
        ]);
    }

    /**
     * Génère un export de tous les bailleurs.
     */
    #[Route(path: '/extract-reclamation/{typeFile}', name: 'extract', requirements: ['typeFile' => '(csv|xlsx)'], defaults: ['typeFile' => 'xlsx'], methods: ['GET'])]
    public function export(
        TransactionRepository $transactionRepository,
        string $typeFile,
        TransactionExporter $transactionExporter
    ): NotFoundHttpException|Response {
        $products = $transactionRepository->findAll();
        $callable = 'exportAs'.strtoupper($typeFile);

        if (is_callable($callable, true, $callableNameFunction)) {
            $result = $transactionExporter->{$callableNameFunction}($products);
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

    #[Route('/annulation/{transactionLine}', name: 'cancel')]
    public function transactionCancel(
        TransactionLine $transactionLine,
        Request $request,
        StripeManager $stripeManager,
        EntityManagerInterface $em,
        MailerManager $mailerManager
    ): Response {
        $data = [
            'amount' => null,
        ];
        $options = [
            'action' => $request->getUri(),
            'maxAmount' => $transactionLine->getAmountTtc() / 100,
        ];
        $form = $this->createForm(CancelTransactionLineType::class, $data, $options);
        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $transactionLine->setCancelTransfertId(
                $stripeManager->cancelTranfert($transactionLine->getTransfertId(), $data['amount'])->id
            )
                ->setCancelAmount($data['amount'] * 100)
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
}
