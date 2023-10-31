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

/**
 * Authentification du Front
 *
 * @Route("/admin/gestion/", name="")
 */
class AuthController extends AbstractController
{
    /**
     * Authentification du Front
     * @Route("login", name="login")
     */
    public function login(SessionInterface $session, Request $request, RouterInterface $router): Response
    {
        $teams = $request->get('teams');
        $joinTeam = $request->get('joinTeam');
        if ($this->getUser() && $teams === null && $joinTeam === null) {
            return $this->redirectToRoute('front_homepage');
        }
        // si on est déjà connecté et que l'on vient d'une email afin de valider un compte
        // invité
        if ($this->getUser() && $teams !== null) {
            return $this->redirectToRoute('front_customer_account_members');
        }
        // si on est déjà connecté et que l'on vient d'une email envoyer par le propriétaire
        // afin de valider un compte d'autoriser un compte à rejoindre son entreprise
        if ($this->getUser() && $joinTeam !== null) {
            return $this->redirectToRoute('front_customer_account_show');
        }

        $referer = $this->generateUrl('front_homepage');
        // on regarde si le lien de provenance est une url de notre application
        foreach ($router->getRouteCollection() as $name => $route) {
            if (str_contains($route->getPath(), $request->headers->get('referer'))) {
                $referer = $request->headers->get('referer');
            }
        }
        // si on a le paramètre dans la route alors on rédirige vers la page mon-equipe
        if (isset($teams)) {
            $referer = 'mon-equipe';
        }
        // si on a le paramètre dans la route alors on rédirige vers la page espace client
        if (isset($joinTeam)) {
            $referer = 'profil';
        }
        // Stockage du referer pour la redirection post login
        $session->set('login_referer', $referer);

        return $this->render('front/auth/login.html.twig');
    }
}
