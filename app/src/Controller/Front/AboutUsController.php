<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;

#[Cache(maxage: '3600')]
class AboutUsController extends AbstractController
{
    #[Route('/a-propos', name: 'aboutus', methods: ['GET'])]
    public function aboutUs(): Response|string
    {
        return $this->render('front/aboutus/index.html.twig');
    }
}
