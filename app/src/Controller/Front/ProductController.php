<?php

namespace App\Controller\Front;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\ReservationRepository;
use App\Service\CartManager;
use App\Service\ProductManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProductController extends AbstractController
{
    /**
     * Affiche un produit en prévisualisation
     * @param string $token
     * @param ProductRepository $productRepository
     * @return Response
     */
    #[Route('/produit/{token}', name: 'product_show', methods: ['GET'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function show(string $token, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findOneBy(['token' => $token]);

        return $this->render('front/product/show.html.twig', compact('product'));
    }

    /**
     * Affiche la page détails d'un produit
     * @param string $token
     * @param ProductRepository $productRepository
     * @return Response
     */
    #[Route('/produit-detail/{token}', name: 'product_show_detail', methods: ['GET'])]
    public function showDetail(string $token, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findOneBy(['token' => $token]);

        return $this->render('front/product/show_detail.html.twig', compact('product'));
    }

    /**
     * Affiche la page de réservation d'un produit
     * @param string $token
     * @param ProductRepository $productRepository
     * @return Response
     */
    #[Route('/produit-reservation/{token}', name: 'product_reservation', methods: ['GET'])]
    public function productReservation(string $token, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findOneBy(['token' => $token]);

        return $this->render('front/product/show_reservation.html.twig', compact('product'));
    }

    #[Route('/produit-ajout', name: 'product_new')]
    #[Route('/produit-modification/{token}', name: 'product_edit')]
    #[IsGranted("ROLE_SELLER")]
    public function new(ProductRepository $productRepository, Request $request, ProductManager $productManager, string $token = null): Response
    {
        if (!$product = $productRepository->findOneBy(['token' => $token])) {
            $product = $productManager->createProduct($this->getUser());
        }
        $form = $this->createForm(ProductType::class, $product, ['action' => $request->getRequestUri()]);

        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $pictureFileDatas = $form->get('uploadedPictures')->getData();

            $productManager->saveOrEditProduct($form->getData(), $pictureFileDatas, $product->getId() ? true : false);

            return $this->json(
                [
                    'success' => true,
                    'callback' => 'onProductChange'
                ]
            );
        }

        return $this->render('front/product/_form.html.twig', compact('form'));
    }

    #[Route('/produit/supprimer/{token}', name: 'product_delete', methods: ['POST'])]
    public function delete(string $token, ProductRepository $productRepository, ProductManager $productManager): Response
    {
        $product = $productRepository->findOneBy(['token' => $token]);

        if (!$product) {
            throw $this->createNotFoundException();
        }
        try {
            if ($productManager->deleteProduct($product)) {
                $this->addFlash('success', 'Produit supprimé !');
            }
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
        }

        return $this->redirectToRoute('front_user_account');
    }

    /**
     * Retourne le html correspondant à liste des produits
     */
    #[Route('/produit-mise-a-jour-liste', name: 'product_update_list', options: ["expose" => true], methods: ['GET'])]
    public function productUpdateList(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy(['user' => $this->getUser()]);

        return $this->render(
            'front/user/partials/_list.html.twig',
            compact('products')
        );
    }

    /**
     * Retourne les reservations d'un produit
     * @param string $token
     * @param ReservationRepository $reservationRepository
     * @return JsonResponse
     */
    #[Route('/produit-reservation-info/{token}', name: 'product_reservation_info', options: ['expose' => true], methods: ['GET'])]
    public function info(string $token, ReservationRepository $reservationRepository): JsonResponse
    {
        $resultReservations = '';
        if ($reservations = $reservationRepository->findOneBy(['token' => $token])) {
            foreach ($reservations as $reservation) {
                if ($resultReservations) {
                    $resultReservations .= ",";
                }
                if ($reservation->getStart() != $reservation->getEnd()) {
                    $resultReservations .= "{'from':" . ($reservation->getStart())->format('Y-m-d') . ", 'to':" . ($reservation->getEnd())->format('Y-m-d') . "}";
                } else {
                    $resultReservations .= ($reservation->getStart())->format('Y-m-d');
                }
            }
        }
        $resultReservations = '[' . $resultReservations . ']';

        return $this->json(['response' => $resultReservations]);
    }

    #[Route('/ajout-produit', name: 'product_add_cart', options: ['expose' => true], methods: ['POST'])]
    public function addProductToCart(Request $request, EntityManagerInterface $em, SessionInterface $session): JsonResponse
    {
        $token = $request->request->get('token');
        $product = $em->getRepository(Product::class)->findOneBy(['token' => $token]);
        $quantity = $request->request->get('quantity');
        $flatpickrDate = $request->request->get('startDate');
        $totalQuantity = 0;
        $totalAmount = 0;
        $cart = $session->get('cart', [
            'products' => [],
            'totalQuantity' => 0,
            'totalAmount' => 0,
            'totalTva' => 0,
        ]);
        if (str_contains($flatpickrDate, 'au')) {
            $startDate = new \DateTimeImmutable(trim(explode('au', $flatpickrDate)[0]));
            $endDate = new \DateTimeImmutable(trim(explode('au', $flatpickrDate)[1]));

        } else {
            $startDate = new \DateTimeImmutable($flatpickrDate);
            $endDate = $startDate;
        }
        $cart['products'][$product->getToken()] = [
            'caution' => $product->getCaution(),
            'price' => $product->getAmount(),
            'quantity' => $quantity,
            'flatpickrDate' => $flatpickrDate,
            'startDate' => $startDate->format('d/m/Y'),
            'endDate' => $endDate->format('d/m/Y'),
            'numberDays' => $startDate->diff($endDate)->days === 0 ?? 1,
            'pictureName' => $product->getPictures()->first()->getName(),
            'title' => $product->getTitle()
        ];
        foreach ($cart['products'] as $item) {
            $totalQuantity += (int)$item['quantity'];
            $totalAmount += (int)$item['price'] * (int)$item['quantity'] * (int)$item['numberDays'];
        }
        $cart['totalQuantity'] = $totalQuantity;
        $cart['totalAmount'] = $totalAmount;
        $cart['totalTva'] = $totalAmount * 0.2;

        $session->set('cart', $cart);

        return $this->json(
            [
                'totalQuantity' => $cart['totalQuantity'],
                'totalAmount' => $cart['totalAmount'],
                'totalTva' => $cart['totalTva'],
            ]
        );
    }
}
