<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PricingController extends AbstractController
{
    #[Route('/abonnements', name: 'app_pricing')]
    public function pricing(): Response
    {
        $user = $this->getUser();

        if ($user && $user->getStripeStatus() != "incomplete") {
            
            return $this->redirectToRoute('user_edit');
        }
        return $this->render('pricing/index.html.twig');
    }
}
