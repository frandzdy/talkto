<?php

namespace App\Controller\Front;

use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ClaimController extends AbstractController
{
    #[Route('/reclamation/{token}', name: 'claim_create', methods: ['GET', 'POST'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function show(string $token, ReservationRepository $reservationRepository): Response
    {
        $reservation = $reservationRepository->findOneBy(['token' => $token]);

        return $this->render('front/reservation/show.html.twig', compact('reservation'));
    }
}
