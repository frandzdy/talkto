<?php
    
    namespace App\Controller\Front;
    
    use App\Entity\Product;
    use App\Entity\Reservation;
    use App\Entity\Transaction;
    use App\Entity\TransactionLine;
    use App\Form\LoginType;
    use App\Form\UserPaymentType;
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
        #[Route('/paiement', name: 'stripe_payment_intent', methods: ['GET', 'POST'])]
        public function paymentIntent(
            SessionInterface $session,
            Request $request,
            StripeManager $stripeManager,
            UserManager $userManager,
            AuthenticationUtils $authenticationUtils,
            Security $security,
            EntityManagerInterface $em
        ): Response {
            /**
             * On récupère l'utilisateur connecté
             * si pas connecté alors on crée un compte stripe avec les informations de facturation
             */
            $carts = $session->get('cart', [
                'products' => [],
                'totalQuantity' => 0,
                'totalAmount' => 0,
                'totalTva' => 0,
                'totalAmountTtc' => 0
            ]);

            if (!$carts) {
                return $this->redirectToRoute('front_home');
            }

            $error = $authenticationUtils->getLastAuthenticationError();
            $lastUsername = $authenticationUtils->getLastUsername();

            $user = $this->getUser();
            if (!$user) {
                $user = $userManager->createUser();
            }
            if (!isset($carts['paymentIntentId'])) {
                $paymentIntent = $stripeManager->createPaymentIntent($carts, $user);
                $carts['paymentIntentId'] = $paymentIntent->id;
            } else {
                $paymentIntent = $stripeManager->retrievePaymentIntent($carts['paymentIntentId']);
            }
            $transaction = new Transaction();
            $reservation = new Reservation();
            foreach ($carts['products'] as $token => $cart) {
                $product = $em->getRepository(Product::class)->findOneBy(['token' => $token]);
                if ($product) {
                    $transactionLine = (new TransactionLine())
                        ->setTransaction($transaction)
                        ->setProduct($product)
                        ->setQuantity($cart['quantity'])
                        ->setStartDate($cart['startDate'])
                        ->setEndDate($cart['endDate'])
                    ;
                    $transaction->addTransactionLine($transactionLine);
                    $product->setQuantityAllReadyReserved($cart['quantity'] + $product->getQuantityAllReadyReserved());
                }
            }
            $transaction->setReference(sprintf('#REF_%s_%s', str_pad($transaction->getId(), 6, '0', STR_PAD_LEFT), $user->getId()));
            $transaction->setPaymentIntentId($paymentIntent->id);
            $em->persist($transaction);

            $reservation->setTransaction();
            $reservation->setStatus();

            $em->flush();
            //$carts['reservation'] = $reservation->getId();
            $session->set('cart', $carts);
            $clientSecret = $paymentIntent->client_secret;

            $form = $this->createForm(UserPaymentType::class, $user);

            if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
                $userManager->saveOrEditUser($user);
                $security->login($user, 'frontAuthenticator', 'front');

                return $this->redirectToRoute('front_stripe_payment_intent');
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

        #[Route('/paiement-connexion', name: 'stripe_payment_user_login', options: ['expose' => true] , methods: ['POST'])]
        public function paymentUserLogin(
            SessionInterface $session,
            UserManager $userManager,
            Request $request,
            AuthenticationUtils $authenticationUtils,
            StripeManager $stripeManager
        ): Response {
            $user = $userManager->createUser();
            $form = $this->createForm(LoginType::class, $user);
            $carts = $session->get('cart', [
                'products' => [],
                'totalQuantity' => 0,
                'totalAmount' => 0,
                'totalTva' => 0,
                'totalAmountTtc' => 0
            ]);

            $error = $authenticationUtils->getLastAuthenticationError();
            $lastUsername = $authenticationUtils->getLastUsername();

            if (!isset($carts['paymentIntentId'])) {
                $paymentIntent = $stripeManager->createPaymentIntent($carts, $user);
                $carts['paymentIntentId'] = $paymentIntent->id;
            } else {
                $paymentIntent = $stripeManager->retrievePaymentIntent($carts['paymentIntentId']);
            }
            $session->set('cart', $carts);
            $clientSecret = $paymentIntent->client_secret;

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
        public function checkPaymentUserCreate(SessionInterface $session, UserManager $userManager, StripeManager $stripeManager, Request $request): Response
        {
            $user = $userManager->createUser();
            $form = $this->createForm(UserPaymentType::class, $user);

            if ($form->handleRequest()->isSubmitted() && $form->isValid()) {
                return $this->json(['success' => 'true', 'redirectUrl' => $this->generateUrl('front_stripe_payment_intent')]);
            }

            return $this->render('front/stripe/_form_user_creation.html.twig', ['form' => $form]);
        }

        #[Route('/success', name: 'stripe_success', options: ['expose' => true], methods: ['POST', 'GET'])]
        public function success(
            StripeManager $stripeManager,
            Request $request,
            UserRepository $userRepository,
            UserManager $userManager,
            SessionInterface $session
        ): Response {
            $carts = $session->get('cart', [
                'products' => [],
                'totalQuantity' => 0,
                'totalAmount' => 0,
                'totalTva' => 0,
                'totalAmountTtc' => 0
            ]);
            $paymentIntent = $stripeManager->retrievePaymentIntent($request->query->get('payment_intent'));
            $paymentIntent = $stripeManager->captureAndTransferPaymentIntent($paymentIntent);
            echo '<pre>';
            dump($paymentIntent);
            echo '</pre>';
            echo 'Répertoire : ' . __DIR__ . ' Ligne : ' . __LINE__ . ' Méthode : ' . __METHOD__ . ' Debug Frandzdy';
            die;
//            //$subscription = $stripeManager->retrieveSubcription($sessionStripe->subscription);
//            $user = $userRepository->findOneBy(['stripeCustomerId' => $sessionStripe->customer]);
//            if ($user) {
//
//                return $this->render('front/stripe/success.html.twig', ['user' => $user]);
//            }
            
            return $this->redirectToRoute('front_user_account');
        }
        
        #[Route('/cancel', name: 'stripe_cancel')]
        public function cancel(): Response
        {
            return $this->render('stripe/cancel.html.twig');
        }

        #[Route('/reauth', name: 'stripe_reauth')]
        #[IsGranted("IS_AUTHENTICATED_FULLY")]
        public function reauth(StripeManager $stripeManager, Request $request, UserRepository $userRepository): Response
        {
            return $this->redirect($stripeManager->createAccountLink($this->getUser())->url);
        }

        #[Route('/return', name: 'stripe_return')]
        #[IsGranted("IS_AUTHENTICATED_FULLY")]
        public function return(StripeManager $stripeManager): Response
        {
            $user = $this->getUser();
            $account = $stripeManager->retrieveAccount($user);

            if ($account->details_submitted || $account->charges_enabled) {
                $user->setIsStripeAccountActive(true);
            }

            return $this->redirectToRoute('front_user_account');
        }
    }
