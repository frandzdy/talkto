<?php

namespace App\Controller\Front;

use App\Repository\ReservationRepository;
use App\Repository\TransactionLineRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ReservationController extends AbstractController
{
    #[Route('/reservation/{token}', name: 'reservation_show', methods: ['GET'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function show(string $token, ReservationRepository $reservationRepository): Response
    {
        $reservation = $reservationRepository->findOneBy(['token' => $token]);

        return $this->render('front/reservation/show.html.twig', compact('reservation'));
    }

    #[Route('/reservation-bailleur/{token}', name: 'reservation_line_show', methods: ['GET'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function lineShow(string $token, TransactionLineRepository $transactionLineRepository, ReservationRepository $reservationRepository): Response
    {
        $transactionLine = $transactionLineRepository->findOneBy(['token' => $token]);
        $reservation = $reservationRepository->findOneBy(['transaction' => $transactionLine->getTransaction()]);

        return $this->render('front/reservation/line-show.html.twig', compact('reservation', 'transactionLine'));
    }
}
