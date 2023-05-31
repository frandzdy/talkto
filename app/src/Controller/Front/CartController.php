<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Form\SellerType;
use App\Form\UserType;
use App\Security\FrontAuthenticator;
use App\Service\MailerManager;
use App\Service\StripeManager;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class CartController extends AbstractController
{
    #[Route('/panier-widget', name: 'cart_widget', options: ['expose' => true], methods: ['GET'])]
    public function widget(SessionInterface $session): JsonResponse
    {
        $carts = $session->get(
            'cart',
            [
                'products' => [],
                'totalQuantity' => 0,
                'totalAmount' => 0
            ]
        );

        return $this->json(
            [
                'response' => $this->renderView('front/cart/cart-widget.html.twig', compact('carts')),
                'totalQuantity' => $carts['totalQuantity'],
                'totalAmount' => $carts['totalAmount'],
            ]
        );
    }

    #[Route('/panier', name: 'cart_index', methods: ['GET'])]
    public function index(SessionInterface $session): Response
    {
        $carts = $session->get(
            'cart',
            [
                'products' => [],
                'totalQuantity' => 0,
                'totalAmount' => 0
            ]
        );

        return $this->render('front/cart/cart.html.twig', compact('carts'));
    }

    #[Route('/panier/supprimer/{token}', name: 'cart_delete', methods: ['GET'])]
    public function delete(string $token, SessionInterface $session): Response
    {
        $carts = $session->get(
            'cart',
            [
                'products' => [],
                'totalQuantity' => 0,
                'totalAmount' => 0
            ]
        );

        if ($carts['products'][$token]) {
            unset($carts['products'][$token]);
        }
        $session->set('cart', $carts);

        return $this->redirectToRoute('front_cart_index');
    }

    #[Route('/panier/mise-a-jour', name: 'cart_update', options: ['expose' => true], methods: ['POST'])]
    public function update(Request $request, SessionInterface $session): JsonResponse
    {
        $token = $request->request->get('token');
        $quantity = $request->request->get('quantity');
        $totalQuantity = 0;
        $totalAmount = 0;
        $carts = $session->get(
            'cart',
            [
                'products' => [],
                'totalQuantity' => 0,
                'totalAmount' => 0
            ]
        );

        if ($carts['products'][$token] && $quantity) {
            $carts['products'][$token]['quantity'] = $quantity;
            $carts['products'][$token]['amount'] = $quantity * $carts['products'][$token]['price'];
        } else {
            unset($carts['products'][$token]);
        }
        foreach ($carts['products'] as $item) {
            $totalQuantity += (int)$item['quantity'];
            $totalAmount += (int)$item['amount'];
        }
        $carts['totalQuantity'] = $totalQuantity;
        $carts['totalAmount'] = $totalAmount;

        $session->set('cart', $carts);

        return $this->json(
            [
                'response' => true
            ]
        );
    }
}
