<?php

namespace App\Controller\Front;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use App\Repository\TransactionLineRepository;
use App\Service\StripeManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class ReservationController extends AbstractController
{
    #[Route('/reservation/{token}', name: 'reservation_show', methods: ['GET'])]
    public function show(string $token, ReservationRepository $reservationRepository): Response
    {
        $reservation = $reservationRepository->findOneBy(['token' => $token]);

        return $this->render('front/reservation/show.html.twig', ['reservation' => $reservation]);
    }

    #[Route('/reservation-bailleur/{token}', name: 'reservation_line_show', methods: ['GET'])]
    public function lineShow(
        string $token,
        TransactionLineRepository $transactionLineRepository,
        ReservationRepository $reservationRepository
    ): Response {
        $transactionLine = $transactionLineRepository->findOneBy(['token' => $token]);
        $reservation = $reservationRepository->findOneBy(['transaction' => $transactionLine->getTransaction()]);

        return $this->render('front/reservation/line-show.html.twig', ['reservation' => $reservation, 'transactionLine' => $transactionLine]);
    }

    /**
     * Affiche le lien de la facture.
     */
    #[Route(path: '/reservation-facture/{token}', name: 'user_invoice', methods: ['GET'])]
    public function generateInvoice(string $token, StripeManager $stripeManager, EntityManagerInterface $em): void
    {
        $reservation = $em->getRepository(Reservation::class)->findOneBy(['token' => $token]);
        $invoice = $stripeManager->getInvoice($reservation->getTransaction());

        dump($invoice);
        // $html =  $this->renderView('front/reservation/invoice.html.twig', compact('reservation'));
    }
}
