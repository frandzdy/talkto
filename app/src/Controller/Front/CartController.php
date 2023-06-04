<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

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
                'totalAmountTtc' => 0
            ]
        );

        return $this->json(
            [
                'response' => $this->renderView('front/cart/cart-widget.html.twig', compact('carts')),
                'totalQuantity' => $carts['totalQuantity'],
                'totalAmount' => $carts['totalAmount'],
                'totalTva' => $carts['totalTva'],
                'totalAmountTtc' => $carts['totalAmountTtc']
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
                'totalAmountTtc' => 0
            ]
        );

        return $this->render('front/cart/cart.html.twig', compact('carts'));
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
                'totalAmountTtc' => 0
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
        $receiveCarts = $request->request->all();

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
                'totalAmountTtc' => 0,

            ]
        );

        foreach ($receiveCarts as $receiveCart) {
            if ($carts['products'][$receiveCart['token']] && $receiveCart['quantity']) {
                $carts['products'][$receiveCart['token']]['quantity'] = (int)$receiveCart['quantity'];
                $flatpickrDate = $receiveCart['startDate'];
                if (str_contains($flatpickrDate, 'au')) {
                    $startDate = new \DateTimeImmutable(trim(explode('au', $flatpickrDate)[0]));
                    $endDate = new \DateTimeImmutable(trim(explode('au', $flatpickrDate)[1]));

                } else {
                    $startDate = new \DateTimeImmutable($flatpickrDate);
                    $endDate = $startDate;
                }
                $carts['products'][$receiveCart['token']]['flatpickrDate'] = $flatpickrDate;
                $carts['products'][$receiveCart['token']]['startDate'] = $startDate->format('d/m/Y');
                $carts['products'][$receiveCart['token']]['endDate'] = $endDate->format('d/m/Y');
                $carts['products'][$receiveCart['token']]['numberDays'] = $startDate->diff($endDate)->days;
                $newPrice = (int)$carts['products'][$receiveCart['token']]['price']
                    * (int)$carts['products'][$receiveCart['token']]['quantity']
                    * (int)$carts['products'][$receiveCart['token']]['numberDays'];
            } else {
                unset($carts['products'][$receiveCart['token']]);
            }
        }

        foreach ($carts['products'] as $item) {
            $totalAmount += (int)$item['price'] * (int)$item['quantity'] * (int)$item['numberDays'];
            $totalQuantity = (int)$item['quantity'];
        }
        $carts['totalQuantity'] = $totalQuantity;
        $carts['totalAmount'] = $totalAmount;
        $carts['totalTva'] = $totalAmount * 0.2;
        $carts['totalAmountTtc'] = $totalAmount * 1.2;

        $session->set('cart', $carts);

        return $this->json(
            [
                'newAmount' => $newPrice,
                'totalAmount' => $carts['totalAmount'],
                'totalTva' => $carts['totalTva'],
                'totalAmountTtc' => $carts['totalAmountTtc'],
                'totalQuantity' => $carts['totalQuantity']
            ]
        );
    }
}
