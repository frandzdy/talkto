<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("PUBLIC_ACCESS")]
#[Route('/', name: 'app_right_', methods: ['GET'])]
class RightsController extends AbstractController
{
    #[Route('/politique-de-confidentialite', name: 'policy', methods: ['GET'])]
    public function policy(): Response
    {
        return $this->render('rights/policy.html.twig');
    }
    
    #[Route('/mentions-legales', name: 'legal_mention', methods: ['GET'])]
    public function mentionsLegals(): Response
    {
        return $this->render('rights/mention-legal.html.twig');
    }
    
    #[Route('/conditions-generales-d-utilisation', name: 'cgu', methods: ['GET'])]
    public function cgu(): Response
    {
        return $this->render('rights/cgu.html.twig');
    }

    #[Route('/charte-de-confiance', name: 'trust_chart', methods: ['GET'])]
    public function trustChart(): Response
    {
        return $this->render('rights/trust-chart.html.twig');
    }
    
    #[Route('/cookies', name: 'cookies', methods: ['GET'])]
    public function cookies(): Response
    {
        return $this->render('rights/cookies.html.twig');
    }
}
