<?php

namespace App\Controller\Front;

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

# Authentification du Front
#[Route("/", name: "")]
class AuthController extends AbstractController
{
    # Authentification du Front
    #[Route("login", name: "login")]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

        if ($this->isGranted(User::ROLE_USER) || $this->isGranted(User::ROLE_SELLER)) {
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
//
//    /**
//     * Permet de réinitialiser le mot de passe du front
//     *
//     * @Route("/mot-de-passe-oublie", name="login_forgotten_password", methods={"GET", "POST"})
//     */
//    public function forgottenPassword(Request $request, MailerManager $mailerManager, EntityManagerInterface $entityManager, UserManager $userManager): Response
//    {
//        $form = $this->createForm(EmailRessettingPasswordType::class);
//
//        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
//            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $form->get('email')->getData()]);
//            $userManager->generateLostTokenForUser($user);
//            // on envoie l'email de notification
//            $userManager->saveUser($user);
//            // envoyer un email de validation de compte
//            $mailerManager->sendMailNotification(
//                $user->getEmail(),
//                'front/emails/ressetting_password.html.twig',
//                [
//                    'user' => $user,
//                    'link' => $this->generateUrl(
//                        'front_login_ressetting_password',
//                        [
//                            'token' => $user->getLostPasswordToken()
//                        ],
//                        UrlGeneratorInterface::ABSOLUTE_URL
//                    )
//                ]
//            );
//
//            return $this->redirectToRoute('front_login_confirmation_ressetting');
//        }
//
//        return $this->render('front/auth/forgotten_password.html.twig', ['form' => $form->createView()]);
//    }
//
//
//    /**
//     * Renvoi un email avec un nouveau token
//     *
//     * @Route("renvoi-email-activation-compte", name="login_resend_email_activation_user", methods={"GET"})
//     */
//    public function resendEmailActivation(MailerManager $mailerManager, UserManager $userManager, SessionInterface $session, EntityManagerInterface $entityManager): Response
//    {
//        $userId = $session->get(FrontAuthenticator::RESEND_EMAIL_FOR_USER, null);
//        $user = $entityManager->getRepository(User::class)->findOneBy(['id' => $userId]);
//
//        if ($user) {
//            $userManager->generateVerificationForUser($user);
//            $userManager->saveUser($user);
//            // envoyer un email de validation de compte
//            $mailerManager->sendMailNotification(
//                $user->getEmail(),
//                'front/emails/check_account.html.twig',
//                [
//                    'user' => $user,
//                    'link' => $this->generateUrl(
//                        'front_user_validation_token_account',
//                        [
//                            'token' => $user->getAccountVerificationToken()
//                        ],
//                        UrlGeneratorInterface::ABSOLUTE_URL
//                    )
//                ]
//            );
//
//            return $this->redirectToRoute('front_user_confirmation_reinitialisation');
//        }
//    }
//
//    /**
//     * Permet de valider la modification du mot de passe
//     * @Route("/changement-mot-de-passe/{token}", name="login_ressetting_password", methods={"GET", "POST"})
//     */
//    public function ressettingPassword(string $token, Request $request, EntityManagerInterface $entityManager, UserManager $userManager, UserRepository $userRepository): Response
//    {
//        if (!$token) {
//            return $this->redirectToRoute('front_login_error_ressetting_password');
//        }
//        $user = $userRepository->getUserByValidLostToken($token);
//        if (!$user) {
//            return $this->redirectToRoute('front_login_error_ressetting_password');
//        }
//
//        $form = $this->createForm(RessettingPasswordType::class);
//
//        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
//            $userManager->changeUserPassword($user, $form->get('plainPassword')->getData());
//            $userManager->emptyLostTokenForUser($user);
//            $userManager->saveUser($user);
//
//            return $this->redirectToRoute('front_login_confirmation_ressetting_password');
//        }
//
//        return $this->render('front/auth/ressetting_password.html.twig', ['form' => $form->createView()]);
//    }
//
//    /**
//     * Retourne le message suite à une action de modification du mot de passe
//     *
//     * @Route("/confirmation-mot-de-passe", name="login_confirmation_ressetting", methods={"GET"})
//     */
//    public function confirmationRessetting(): Response
//    {
//        return $this->render('front/auth/confirmation_ressetting.html.twig');
//    }
//
//    /**
//     * Retourne le message suite à une action de confirmation de la modification du mot de passe
//     *
//     * @Route("/confirmation-changement-mot-de-passe", name="login_confirmation_ressetting_password", methods={"GET"})
//     */
//    public function confirmationRessettingPassword(): Response
//    {
//        return $this->render('front/auth/confirmation_password_change.html.twig');
//    }
//
//    /**
//     * Retourne le message suite à une erreur lors d'une tentative de modification de mot de passe
//     *
//     * @Route("/erreur-confirmation-changement-mot-de-passe", name="login_error_ressetting_password", methods={"GET"})
//     */
//    public function errorRessettingPassword(): Response
//    {
//        return $this->render('front/auth/error_ressetting_password.html.twig');
//    }
}
