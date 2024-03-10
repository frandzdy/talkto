<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

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
                'totalAmount' => 0,
                'totalTva' => 0,
                'totalFees' => 0,
                'transactionId' => null,
            ]
        );

        return $this->json(
            [
                'response' => $this->renderView('front/cart/cart-widget.html.twig', ['carts' => $carts]),
                'totalQuantity' => $carts['totalQuantity'],
                'totalAmount' => $carts['totalAmount'],
                'totalTva' => $carts['totalTva'],
                'totalFees' => $carts['totalFees'],
            ]
        );
    }

    #[Route('/panier', name: 'cart_index', options: ['expose' => true], methods: ['GET'])]
    public function index(SessionInterface $session): Response
    {
        $carts = $session->get(
            'cart',
            [
                'products' => [],
                'totalQuantity' => 0,
                'totalAmount' => 0,
                'totalTva' => 0,
                'totalFees' => 0,
                'transactionId' => null,
            ]
        );

        return $this->render('front/cart/cart.html.twig', ['carts' => $carts]);
    }

    #[Route('/panier/supprimer/{token}', name: 'cart_delete', methods: ['POST'])]
    public function delete(string $token, SessionInterface $session): Response
    {
        $carts = $session->get(
            'cart',
            [
                'products' => [],
                'totalQuantity' => 0,
                'totalAmount' => 0,
                'totalTva' => 0,
                'totalFees' => 0,
                'transactionId' => null,
            ]
        );

        if (array_key_exists($token, $carts['products'])) {
            $carts['totalQuantity'] -= $carts['products'][$token]['quantity'];
            $carts['totalAmount'] -= $carts['products'][$token]['price']
                * $carts['products'][$token]['numberDays']
                * $carts['products'][$token]['quantity'] + ($carts['products'][$token]['price']
                    * $carts['products'][$token]['numberDays']
                    * $carts['products'][$token]['quantity'] * 0.1);
            $carts['totalTva'] = $carts['totalAmount'] * 0.2;
            unset($carts['products'][$token]);
        }

        if (0 === count($carts['products'])) {
            $session->set('cart', [
                'products' => [],
                'totalQuantity' => 0,
                'totalAmount' => 0,
                'totalTva' => 0,
                'totalFees' => 0,
                'transactionId' => null,
            ]);
        } else {
            $session->set('cart', $carts);
        }

        return $this->redirectToRoute('front_cart_index');
    }

    #[Route('/panier/mise-a-jour', name: 'cart_update', options: ['expose' => true], methods: ['POST'])]
    public function update(Request $request, SessionInterface $session): JsonResponse
    {
        $startDate = null;
        $endDate = null;
        $newPrice = 0;
        $totalQuantity = 0;
        $totalAmount = 0;
        $carts = $session->get(
            'cart',
            [
                'products' => [],
                'totalQuantity' => 0,
                'totalAmount' => 0,
                'totalTva' => 0,
                'totalFees' => 0,
                'transactionId' => null,
            ]
        );
        $products = [];
        foreach (json_decode($request->request->get('carts'), true) as $product) {
            $products[$product['token']] = $product;
        }

        foreach ($products as $token => $product) {
            if ($carts['products'][$token] && $product['quantity']) {
                $carts['products'][$token]['quantity'] = (int) $product['quantity'];
                $flatpickrDate = $product['startDate'];
                if (str_contains((string) $flatpickrDate, 'au')) {
                    $startDate = new \DateTimeImmutable(trim(explode('au', (string) $flatpickrDate)[0]));
                    $endDate = new \DateTimeImmutable(trim(explode('au', (string) $flatpickrDate)[1]));
                } else {
                    $startDate = new \DateTimeImmutable($flatpickrDate);
                    $endDate = $startDate;
                }

                $carts['products'][$token]['flatpickrDate'] = $flatpickrDate;
                $carts['products'][$token]['startDate'] = $startDate->format('d/m/Y');
                $carts['products'][$token]['endDate'] = $endDate->format('d/m/Y');
                $carts['products'][$token]['numberDays'] = 0 === $startDate->diff(
                    $endDate
                )->days ? 1 : $startDate->diff($endDate)->days;
                $newPrice = (int) $carts['products'][$token]['price']
                    * (int) $carts['products'][$token]['quantity']
                    * (int) $carts['products'][$token]['numberDays'];
            } else {
                unset($carts['products'][$token]);
            }
        }

        foreach ($carts['products'] as $item) {
            $totalAmount += (int) $item['price'] * (int) $item['quantity'] * (int) $item['numberDays'];
            $totalQuantity = (int) $item['quantity'];
        }

        $carts['totalQuantity'] = $totalQuantity;
        $carts['totalAmount'] = $totalAmount + ($totalAmount * 0.1);
        $carts['totalTva'] = $totalAmount * 0.2;
        $carts['totalFees'] = $totalAmount * 0.1;

        $session->set('cart', $carts);

        return $this->json(
            [
                'newAmount' => $newPrice,
                'totalAmount' => $carts['totalAmount'],
                'totalTva' => $carts['totalTva'],
                'totalQuantity' => $carts['totalQuantity'],
            ]
        );
    }
}
