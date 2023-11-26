<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("PUBLIC_ACCESS")]
#[Route('/', name: '')]
class RightsController extends AbstractController
{
    #[Route('politique-de-confidentialite', name: 'policy', methods: ['GET'])]
    public function policy(): Response
    {
        return $this->render('front/rights/policy.html.twig');
    }
    
    #[Route('mentions-legales', name: 'legal_mention', methods: ['GET'])]
    public function mentionsLegals(): Response
    {
        return $this->render('front/rights/mention-legal.html.twig');
    }
    
    #[Route('conditions-generales-d-utilisation', name: 'cgu', methods: ['GET'])]
    public function cgu(): Response
    {
        return $this->render('front/rights/cgu.html.twig');
    }

    #[Route('gestion-donnees-personnelles', name: 'personal_data', methods: ['GET'])]
    public function personalData(): Response
    {
        return $this->render('front/rights/handle-personal-data.html.twig');
    }

    #[Route('charte-de-confiance', name: 'trust_chart', methods: ['GET'])]
    public function trustChart(): Response
    {
        return $this->render('front/rights/trust-chart.html.twig');
    }
    
    #[Route('cookies', name: 'cookies', methods: ['GET'])]
    public function cookies(): Response
    {
        return $this->render('front/rights/cookies.html.twig');
    }
}
