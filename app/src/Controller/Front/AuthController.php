<?php

namespace App\Controller\Front;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

# Authentification du Front
#[Route("/", name: "")]
class AuthController extends AbstractController
{
    # Authentification du Front
    #[Route("login", name: "login")]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('front_home');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'front/auth/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error' => $error
            ]
        );
    }
    #[Route("deconnexion", name: "logout")]
    public function logout()
    {

    }
}
