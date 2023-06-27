<?php

namespace App\Controller\Front;

use App\Entity\Transaction;
use App\Enum\TransactionStatus;
use App\Form\LoginType;
use App\Form\UserPaymentType;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use App\Service\StripeManager;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class StripeController extends AbstractController
{
    #[Route('/paiement', name: 'stripe_payment_intent', options: ['expose' => true], methods: ['GET', 'POST'])]
    public function paymentIntent(
        SessionInterface       $session,
        Request                $request,
        StripeManager          $stripeManager,
        UserManager            $userManager,
        AuthenticationUtils    $authenticationUtils,
        Security               $security,
        EntityManagerInterface $em
    ): Response
    {
        /**
         * On récupère l'utilisateur connecté
         * si pas connecté alors on crée un compte stripe avec les informations de facturation
         */
        $carts = $session->get('cart', [
            'products' => [],
            'totalQuantity' => 0,
            'totalAmount' => 0,
            'totalTva' => 0,
            'totalFees' => 0,
            'paymentIntentId' => null,
            'transactionId' => null
        ]);

        if (!$carts['products']) {
            return $this->redirectToRoute('front_home');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $user = $this->getUser();
        if (!$user) {
            $user = $userManager->createUser();
        } else {
            if (!$carts['transactionId']) {
                $transaction = (new Transaction())
                    ->setStatus(TransactionStatus::WAITING)
                    ->setToken(hash('sha256', random_bytes(32)))
                    ->setAuthor($user);
                $stripeManager->addOrUpdateTransactionLine($carts['products'], $transaction);
                $em->persist($transaction);
                $em->flush();
                $transaction->setReference(sprintf('#REF_%s', str_pad((string)$transaction->getId(), 6, '0', STR_PAD_LEFT)));
                if (!isset($carts['paymentIntentId'])) {
                    $paymentIntent = $stripeManager->createPaymentIntent($carts, $user, $transaction);
                    $carts['paymentIntentId'] = $paymentIntent->id;
                } else {
                    $paymentIntent = $stripeManager->retrievePaymentIntent($carts['paymentIntentId']);
                }
                $transaction->setPaymentIntentId($paymentIntent->id);
                $em->flush();
                $carts['transactionId'] = $transaction->getId();
            } else {
                $transaction = $em->getRepository(Transaction::class)->findOneBy(['id' => $carts['transactionId']]);
                $paymentIntent = $stripeManager->retrievePaymentIntent($carts['paymentIntentId']);
                $paymentIntent = $stripeManager->updatePaymentIntent($paymentIntent->id, $carts);
                foreach ($transaction->getTransactionLines() as $transactionLine) {
                    $transaction->removeTransactionLine($transactionLine);
                }
                // mettre à jour aussi la transaction
                $stripeManager->addOrUpdateTransactionLine($carts['products'], $transaction);
                $em->flush();
            }

            $session->set('cart', $carts);
            $clientSecret = $paymentIntent->client_secret;
        }

        $form = $this->createForm(UserPaymentType::class, $user, ['isOnline' => $this->getUser() ?? false]);

        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $userManager->saveOrEditUser(user: $user, isGuess: true);
            $security->login($user, 'App\Security\FrontAuthenticator', 'front');

            return $this->redirectToRoute('front_stripe_payment_intent');
        }

        return $this->render(
            'front/stripe/checkout.html.twig',
            [
                'last_username' => $lastUsername,
                'carts' => $carts,
                'clientSecret' => $clientSecret ?? null,
                'form' => $form,
                'error' => $error,
                'transaction' => $transaction ?? null
            ]
        );
    }

    #[Route('/paiement-connexion', name: 'stripe_payment_user_login', options: ['expose' => true], methods: ['POST'])]
    public function paymentUserLogin(
        SessionInterface       $session,
        UserManager            $userManager,
        Request                $request,
        AuthenticationUtils    $authenticationUtils
    ): Response
    {
        $user = $userManager->createUser();
        $form = $this->createForm(LoginType::class, $user);
        $carts = $session->get('cart', [
            'products' => [],
            'totalQuantity' => 0,
            'totalAmount' => 0,
            'totalTva' => 0,
            'totalFees' => 0,
            'paymentIntentId' => null,
            'transactionId' => null
        ]);
        $clientSecret = null;
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            return $this->json(['success' => 'true', 'redirectUrl' => $this->generateUrl('front_stripe_payment_intent')]);
        }

        return $this->render(
            'front/stripe/checkout.html.twig',
            [
                'last_username' => $lastUsername,
                'carts' => $carts,
                'clientSecret' => $clientSecret,
                'form' => $form,
                'error' => $error
            ]
        );
    }

    #[Route('/paiement-user-creation', name: 'stripe_payment_user_create', options: ['expose' => true], methods: ['GET'])]
    public function checkPaymentUserCreate(UserManager $userManager): Response
    {
        $user = $userManager->createUser();
        $form = $this->createForm(UserPaymentType::class, $user);

        if ($form->handleRequest()->isSubmitted() && $form->isValid()) {
            return $this->json(['success' => 'true', 'redirectUrl' => $this->generateUrl('front_stripe_payment_intent')]);
        }

        return $this->render('front/stripe/_form_user_creation.html.twig', ['form' => $form]);
    }

    #[Route('/success', name: 'stripe_success', options: ['expose' => true], methods: ['POST', 'GET'])]
    public function success(StripeManager $stripeManager, Request $request): Response
    {
        $paymentIntent = $stripeManager->retrievePaymentIntent($request->query->get('payment_intent'));
        $message = 'Erreur lors du paiement';
        switch ($paymentIntent->status) {
            case 'succeeded':
                $message = 'Paiement validé';
                break;
            case 'processing':
                $message = 'Paiement en cour de validation';
                break;
            case 'requires_payment_method':
                return $this->redirectToRoute('front_stripe_payment_intent');
            default:
                break;
        }

        return $this->render('front/stripe/success.html.twig', ['user' => $this->getUser(), 'message' => $message]);
    }

    #[Route('/cancel', name: 'stripe_cancel')]
    public function cancel(): Response
    {
        return $this->render('stripe/cancel.html.twig');
    }

    #[Route('/reauth', name: 'stripe_reauth')]
    #[Security("is_granted('ROLE_SELLER') and is_granted('IS_AUTHENTICATED_FULLY')")]
    public function reauth(StripeManager $stripeManager): Response
    {
        return $this->redirect($stripeManager->createAccountLink($this->getUser())->url);
    }

    #[Route('/return', name: 'stripe_return')]
    #[Security("is_granted('ROLE_SELLER') and is_granted('IS_AUTHENTICATED_FULLY')")]
    public function return(StripeManager $stripeManager, UserManager $userManager): Response
    {
        $user = $this->getUser();
        $account = $stripeManager->retrieveAccount($user);

        if ($account->details_submitted || $account->charges_enabled) {
            $user->setIsStripeAccountActive(true);
        }
        $userManager->saveUser();

        return $this->redirectToRoute('front_user_account');
    }
}
