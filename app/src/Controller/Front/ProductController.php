<?php

namespace App\Controller\Front;

use App\Entity\Picture;
use App\Entity\Product;
use App\Enum\ProductCategory;
use App\Form\Front\ProductReservationType;
use App\Form\Front\ProductType;
use App\Repository\ProductRepository;
use App\Service\ProductManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
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
     *
     * @param string $token
     * @param ProductRepository $productRepository
     * @return Response
     */
    #[Route('/produit-previsualisation/{token}', name: 'product_show', methods: ['GET'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function show(string $token, ProductRepository $productRepository): Response
    {
        $noteReview = 0;
        $product = $productRepository->findOneBy(['token' => $token]);

        return $this->render('front/product/show_preview.html.twig', compact('product', 'noteReview'));
    }

    /**
     * Affiche la page détails d'un produit
     *
     * @param string $token
     * @param ProductRepository $productRepository
     * @return Response
     */
    #[Route('/produit-previsualisation-details/{token}', name: 'product_show_detail', methods: ['GET'])]
    public function showDetails(string $token, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findOneBy(['token' => $token]);

        return $this->render('front/product/show_preview_detail.html.twig', compact('product'));
    }

    /**
     * Affiche la page de réservation d'un produit
     *
     * @param string $token
     * @param ProductRepository $productRepository
     * @param Request $request
     * @param $productManager $productManager
     * @return Response
     */
    #[Route('/produit-reservation/{token}', name: 'product_reservation', methods: ['GET', 'POST'])]
    #[Route('/produit-reservation-details/{token}', name: 'product_reservation_show_detail', methods: ['GET', 'POST'])]
    public function productReservation(
        string $token,
        ProductRepository $productRepository,
        Request $request,
        ProductManager $productManager,
        SessionInterface $session
    ): Response {
        $product = $productRepository->findOneBy(['token' => $token]);
        $data = [
            'date' => null,
            'quantity' => null
        ];
        $choicesValue = [];
        $quantityLeft = $product->getQuantity() - $product->getQuantityAllReadyReserved();
        for ($i = 1; $i <= $quantityLeft + 1; $i++) {
            $choicesValue[$i] = $i;
        }

        $options = [
            'quantityLeft' => $quantityLeft,
            'action' => $request->getRequestUri(),
            'choicesValue' => $choicesValue,
            'disabledDates' => $productManager->getDisabledDatesFormProduct($token)
        ];

        $form = $this->createForm(ProductReservationType::class, $data, $options);

        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $flatpickrDate = $data['date'];
            $quantity = $data['quantity'];
            $cart = $session->get('cart', [
                'products' => [],
                'totalQuantity' => 0,
                'totalAmount' => 0,
                'totalTva' => 0,
                'totalFees' => 0,
                'transactionId' => null
            ]);

            $cart = $productManager->addProductToCart($cart, $flatpickrDate, $product, $quantity);

            $session->set('cart', $cart);
            $this->addFlash('success', 'Produit(s) ajouté(s)');

            if ($request->attributes->get('_route') === "front_product_reservation_show_detail") {

                return $this->redirectToRoute('front_product_reservation_show_detail', ['token' => $token]);
            }

            return $this->json(
                [
                    'success' => true,
                    'redirectUrl' => $this->generateUrl('front_home')
                ]
            );
        }

        if ($request->attributes->get('_route') === "front_product_reservation") {

            return $this->render('front/product/show_reservation.html.twig', compact('product', 'quantityLeft', 'form'));
        }

        return $this->render('front/product/show_reservation_detail.html.twig', compact('product', 'quantityLeft', 'form'));
    }

    #[Route('/produit-ajout', name: 'product_new')]
    #[Route('/produit-modification/{token}', name: 'product_edit')]
    #[IsGranted("ROLE_SELLER")]
    public function new(
        ProductRepository $productRepository,
        Request $request,
        ProductManager $productManager,
        string $token = null
    ): Response {
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
                    'redirectUrl' => $this->generateUrl('front_user_account')
                ]
            );
        }

        return $this->render('front/product/_form.html.twig', compact('form'));
    }

    #[Route('/produit/supprimer/{token}', name: 'product_delete', methods: ['POST'])]
    #[IsGranted("ROLE_SELLER")]
    public function delete(
        string $token,
        ProductRepository $productRepository,
        ProductManager $productManager
    ): Response {
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
        $products = $productRepository->findBy(['author' => $this->getUser()]);
        $results['results'] = $products;
        return $this->render(
            'front/user/partials/_products.html.twig',
            compact('products')
        );
    }

    #[Route('/produit/image/suppression/{token}', name: 'product_picture_delete', options: ['expose' => true], methods: ['POST'])]
    #[IsGranted('ROLE_SELLER')]
    public function productPictureDelete(string $token, EntityManagerInterface $em): JsonResponse
    {
        if ($picture = $em->getRepository(Picture::class)->findOneBy(['token' => $token])) {
            $em->remove($picture);
            $em->flush();

            return $this->json(
                [
                    'success' => true,
                ]
            );
        }

        return $this->json(['token' => null], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    #[Route('/produit/categorie/{productCategory}', name: 'product_category', options: ['expose' => true], methods: [
        'GET',
        'POST'
    ])]
    public function productCategory(
        ProductCategory $productCategory,
        EntityManagerInterface $em,
        Request $request,
        PaginatorInterface $paginator,
        SessionInterface $session
    ): Response {
        $amount = $request->query->get('amount', '0-500');
        $distance = $request->query->get('distance', '0-5');
        $sortedBy = $request->query->getInt('sortedBy', 1);

        $user = $this->getUser();
        if (!$user) {
            $lat = $session->get('lat', 0);
            $lon = $session->get('lon', 0);
        } else {
            $lat = $user->getLat();
            $lon = $user->getLon();
        }
        $filter = [
            'startAmount' => explode('-', $amount)[0],
            'endAmount' => explode('-', $amount)[1],
            'startDistance' => explode('-', $distance)[0],
            'endDistance' => explode('-', $distance)[1],
            'sortedBy' => $sortedBy,
            'category' => $productCategory->value,
            'lon' => $lon,
            'lat' => $lat
        ];
        $queryProducts = $em->getRepository(Product::class)->getFilteredProducts($filter);
        $data = [
            'amount' => $amount,
            'distance' => $distance,
            'sortedBy' => $sortedBy
        ];

        $products = $paginator->paginate(
            $queryProducts, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            20 /*limit per page*/,
            ['wrap-queries' => true]
        );

        return $this->render(
            'front/product/category.html.twig',
            [
                'pagination' => $products,
                'productCategory' => $productCategory,
                'data' => $data
            ]
        );
    }

    #[Route('/produit-recherche', name: 'product_search', methods: ['GET'])]
    public function productSearch(
        ProductRepository $productRepository,
        Request $request,
        PaginatorInterface $paginator,
        SessionInterface $session
    ): Response {
        $user = $this->getUser();
        if ($user) {
            $lat = $user->getLat();
            $lon = $user->getLon();
        } else {
            $lat = $session->get('lat', 0);
            $lon = $session->get('lon', 0);
        }
        $sortedBy = $request->query->getInt('sortedBy', 1);
        $searchProducts = $request->query->get('searchProducts', null);
        $filter = [
            'searchIds' => $searchProducts,
            'startDistance' => 0,
            'endDistance' => 1000,
            'sortedBy' => $sortedBy,
            'lon' => $lon,
            'lat' => $lat
        ];
        $querysearchProducts = $productRepository->searchProducts($filter);
        $data = [
            'sortedBy' => $sortedBy,
            'searchProducts' => $searchProducts
        ];

        $searchProducts = $paginator->paginate(
            $querysearchProducts, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            20 /*limit per page*/,
            ['wrap-queries' => true]
        );

        return $this->render(
            'front/product/search.html.twig',
            [
                'pagination' => $searchProducts,
                'data' => $data
            ],
        );
    }
}
