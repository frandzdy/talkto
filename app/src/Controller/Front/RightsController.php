<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Cache(maxage: '+ 1 years')]
class RightsController extends AbstractController
{
    #[Route('/politique-de-confidentialite', name: 'policy', methods: ['GET'])]
    public function policy(): Response
    {
        return $this->render('front/rights/policy.html.twig')->setMaxAge("3600");
    }

    #[Route('/mentions-legales', name: 'legal_mention', methods: ['GET'])]
    #[Cache(maxage: '+ 1 years')]
    public function mentionsLegals(): Response
    {
        return $this->render('front/rights/mention-legal.html.twig');
    }

    #[Route('/conditions-generales-d-utilisation', name: 'cgu', methods: ['GET'])]
    public function cgu(): Response
    {
        return $this->render('front/rights/cgu.html.twig')->setMaxAge("3600");
    }

    #[Route('/gestion-donnees-personnelles', name: 'personal_data', methods: ['GET'])]
    #[Cache(maxage: '+ 1 years')]
    public function personalData(): Response
    {
        return $this->render('front/rights/handle-personal-data.html.twig');
    }

    #[Route('/charte-de-confiance', name: 'trust_chart', methods: ['GET'])]
    #[Cache(maxage: '+ 1 years')]
    public function trustChart(): Response
    {
        return $this->render('front/rights/trust-chart.html.twig');
    }

    #[Route('/cookies', name: 'cookies', methods: ['GET'])]
    public function cookies(): Response
    {
        return $this->render('front/rights/cookies.html.twig')->setMaxAge("3600");
    }
}
