<?php

namespace App\Controller\Front;

use App\Form\Front\LoginType;
use App\Form\Front\UserPaymentType;
use App\Security\FrontAuthenticator;
use App\Service\StripeManager;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class StripeController extends AbstractController
{
    #[Route('/paiement', name: 'stripe_payment_intent', options: ['expose' => true], methods: ['GET', 'POST'])]
    public function paymentIntent(
        SessionInterface $session,
        Request $request,
        StripeManager $stripeManager,
        UserManager $userManager,
        AuthenticationUtils $authenticationUtils,
        Security $security
    ): Response {
        /**
         * On récupère l'utilisateur connecté
         * si pas connecté alors, on crée un compte stripe avec les informations de facturation.
         */
        $carts = $session->get('cart', [
            'products' => [],
            'totalQuantity' => 0,
            'totalAmount' => 0,
            'totalTva' => 0,
            'totalFees' => 0,
            'transactionId' => null,
        ]);
        $clientSecret = null;
        $transaction = null;
        if (!$carts['products']) {
            return $this->redirectToRoute('front_home');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $user = $this->getUser();
        if (!$user instanceof UserInterface) {
            $user = $userManager->createUser();
        } else {
            [$paymentIntent, $transaction] = $stripeManager->createTransaction($carts, $user);
            $session->set('cart', $carts);
            $clientSecret = $paymentIntent->client_secret;
        }

        $form = $this->createForm(UserPaymentType::class, $user, ['isOnline' => $this->getUser() ?? false]);

        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $userManager->saveOrEditUser(user: $user, isGuess: true);
            $security->login($user, FrontAuthenticator::class, 'front');

            return $this->redirectToRoute('front_stripe_payment_intent');
        }

        return $this->render(
            'front/stripe/checkout.html.twig',
            [
                'last_username' => $lastUsername,
                'carts' => $carts,
                'clientSecret' => $clientSecret,
                'form' => $form,
                'error' => $error,
                'transaction' => $transaction,
            ]
        );
    }

    #[Route('/paiement-connexion', name: 'stripe_payment_user_login', options: ['expose' => true], methods: ['POST'])]
    public function paymentUserLogin(
        SessionInterface $session,
        UserManager $userManager,
        Request $request,
        AuthenticationUtils $authenticationUtils
    ): Response {
        $user = $userManager->createUser();
        $form = $this->createForm(LoginType::class, $user);
        $carts = $session->get('cart', [
            'products' => [],
            'totalQuantity' => 0,
            'totalAmount' => 0,
            'totalTva' => 0,
            'totalFees' => 0,
            'transactionId' => null,
        ]);
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Connexion effectué.');

            return $this->json(
                ['success' => 'true', 'redirectUrl' => $this->generateUrl('front_stripe_payment_intent')]
            );
        }

        return $this->render(
            'front/stripe/checkout.html.twig',
            [
                'last_username' => $lastUsername,
                'carts' => $carts,
                'clientSecret' => null,
                'form' => $form,
                'error' => $error,
            ]
        );
    }

    #[Route('/paiement-user-creation', name: 'stripe_payment_user_create', options: ['expose' => true], methods: ['GET'])]
    public function checkPaymentUserCreate(UserManager $userManager): Response
    {
        $user = $userManager->createUser();
        $form = $this->createForm(UserPaymentType::class, $user);

        if ($form->handleRequest()->isSubmitted() && $form->isValid()) {
            if ($user->getIsGuess()) {
                $this->addFlash('success', 'Compte invité(e) enregistré.');
            } else {
                $this->addFlash('success', 'Compte enregistré.');
            }
            $userManager->saveOrEditUser($user, isGuess: $user->getIsGuess());

            return $this->json(
                [
                    'success' => 'true',
                    'redirectUrl' => $this->generateUrl('front_stripe_payment_intent'),
                ]
            );
        }

        return $this->render('front/stripe/_form_user_creation.html.twig', ['form' => $form]);
    }

    #[Route('/valider', name: 'stripe_success', options: ['expose' => true], methods: ['POST', 'GET'])]
    public function success(StripeManager $stripeManager, Request $request, SessionInterface $session): Response
    {
        $paymentIntent = $stripeManager->retrievePaymentIntent($request->query->get('payment_intent'));
        $message = 'Erreur lors du paiement.';
        $error = false;

        switch ($paymentIntent->status) {
            case 'succeeded':
                $message = 'Paiement validé !';
                $session->set('cart', [
                    'products' => [],
                    'totalQuantity' => 0,
                    'totalAmount' => 0,
                    'totalTva' => 0,
                    'totalFees' => 0,
                    'transactionId' => null,
                ]);

                break;

            case 'processing':
                $message = 'Paiement en cour de validation !';
                $session->set('cart', [
                    'products' => [],
                    'totalQuantity' => 0,
                    'totalAmount' => 0,
                    'totalTva' => 0,
                    'totalFees' => 0,
                    'transactionId' => null,
                ]);

                break;

            case 'requires_payment_method':
                $message = 'Veuillez choisir un autre moyen de paiement !';
                $error = true;

                break;

            default:
                break;
        }

        return $this->render(
            'front/stripe/success.html.twig',
            ['user' => $this->getUser(), 'message' => $message, 'error' => $error]
        );
    }

    #[Route('/annuler', name: 'stripe_cancel')]
    public function cancel(): Response
    {
        return $this->render('front/stripe/cancel.html.twig');
    }

    #[Route('/commercial/reauthentification', name: 'stripe_reauth')]
    #[IsGranted('ROLE_SELLER')]
    public function reauth(StripeManager $stripeManager): Response
    {
        return $this->redirect($stripeManager->createAccountLink($this->getUser())->url);
    }

    #[Route('/commercial/retour', name: 'stripe_return')]
    #[IsGranted('ROLE_SELLER')]
    public function return(StripeManager $stripeManager, UserManager $userManager): Response
    {
        $user = $this->getUser();
        $account = $stripeManager->retrieveAccount($user);

        if ($account->details_submitted || $account->charges_enabled) {
            $user->setIsStripeAccountActive(true);
            $userManager->saveUser();
        }

        return $this->redirectToRoute('front_user_account');
    }
}
