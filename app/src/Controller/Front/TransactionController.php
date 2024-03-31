<?php

namespace App\Controller\Front;

use App\Enum\TransactionLineStatus;
use App\Repository\TransactionLineRepository;
use App\Repository\TransactionRepository;
use App\Service\MailerManager;
use App\Service\StripeManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class TransactionController extends AbstractController
{
    #[Route('/transaction/{token}', name: 'transaction_show', methods: ['GET'])]
    public function transaction(string $token, TransactionRepository $transactionRepository): Response
    {
        $transaction = $transactionRepository->findOneBy(['token' => $token]);

        return $this->render('front/product/show.html.twig', ['transaction' => $transaction]);
    }

    #[Route('/ligne-transaction/annulation/{token}', name: 'transaction_line_delete', methods: ['GET'])]
    public function transactionLineCanceled(
        string $token,
        TransactionLineRepository $transactionLineRepository,
        StripeManager $stripeManager,
        EntityManagerInterface $em,
        MailerManager $mailerManager
    ): Response {
        $transactionLine = $transactionLineRepository->findOneBy(['token' => $token]);

        $refund = $stripeManager->refundTransactionLine($transactionLine);

        if ('failed' === $refund->status) {
            $this->addFlash('error', 'Veuillez contrôler vos informations bancaire.');
        } else {
            $transactionLine->setStatus(TransactionLineStatus::CANCELED);
            $this->addFlash('success', 'Remboursement effectué.');
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
        }

        return $this->redirectToRoute('front_account_show');
    }
}
