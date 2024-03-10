<?php

namespace App\Controller\Front;

use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
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
}
