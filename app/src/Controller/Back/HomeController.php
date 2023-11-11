<?php

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/', name: 'home_', methods: ['GET', 'POST'])]
class HomeController extends AbstractController
{
    #[Route(path: '', name: 'dashboard', methods: ['GET', 'POST'])]
    public function dashboard(Request $request): Response
    {

        return $this->render('back/home/dashboard.html.twig');
    }
}
