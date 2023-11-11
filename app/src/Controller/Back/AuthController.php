<?php

namespace App\Controller\Back;

use App\Entity\CustomerAccount;
use App\Entity\User;
use App\Form\Type\Front\CustomerAccountCreationType;
use App\Form\Type\Front\EmailRessettingPasswordType;
use App\Form\Type\Front\RessettingPasswordType;
use App\Repository\CustomerAccountRepository;
use App\Repository\UserRepository;
use App\Security\FrontAuthenticator;
use App\Service\MailerManager;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Authentification du Back
 *
 */
class AuthController extends AbstractController
{
    /**
     * Authentification du Back
     */
    #[Route("/login", name: "login")]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('back_home_dashboard');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'back/auth/login.html.twig',
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
