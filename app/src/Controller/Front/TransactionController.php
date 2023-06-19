<?php

namespace App\Controller\Front;

use App\Enum\TransactionLineStatus;
use App\Repository\TransactionLineRepository;
use App\Repository\TransactionRepository;
use App\Service\StripeManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("IS_AUTHENTICATED_FULLY")]
class TransactionController extends AbstractController
{
    #[Route('/transaction/{token}', name: 'transaction_show', methods: ['GET'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function transaction(string $token, TransactionRepository $transactionRepository): Response
    {
        $transaction = $transactionRepository->findOneBy(['token' => $token]);

        return $this->render('front/product/show.html.twig', compact('transaction'));
    }

    #[Route('/ligne-transaction/annulation/{token}', name: 'transaction_line_delete', methods: ['GET'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function transactionLineCanceled(
        string $token,
        TransactionLineRepository $transactionLineRepository,
        StripeManager $stripeManager
    ): Response {
        $transactionLine = $transactionLineRepository->findOneBy(['token' => $token]);

        $refund = $stripeManager->refundTransactionLine($transactionLine);

        if ($refund->status === 'failed') {
            $this->addFlash('error', 'Veuillez contrôler vos informations bancaire.');
        } else {
            $transactionLine->setStatus(TransactionLineStatus::CANCELED);
            $this->addFlash('success', 'Votre demande de remboursement est pris en compte.');
        }

        return $this->redirectToRoute('front_account_show');
    }
}
