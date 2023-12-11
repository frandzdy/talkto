<?php

namespace App\Controller\Front;

use App\Entity\Picture;
use App\Entity\Product;
use App\Entity\Review;
use App\Entity\TransactionLine;
use App\Enum\ProductCategory;
use App\Form\Front\ProductReservationType;
use App\Form\Front\ProductReviewType;
use App\Form\Front\ProductType;
use App\Repository\ProductRepository;
use App\Repository\TransactionLineRepository;
use App\Service\ProductManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProductController extends AbstractController
{
    public const MAX_RESULT = 4;

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
        if (!$product) {
            throw $this->createNotFoundException('Ce produit n\'existant pas.');
        }

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
        if (!$product) {
            throw $this->createNotFoundException('Ce produit n\'existe pas.');
        }

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
    #[Route('/produit-reservation/{token}/{review}', name: 'product_reservation', defaults: ['review' => false], methods: [
        'GET',
        'POST'
    ])]
    #[Route('/produit-reservation-details/{token}/{review}', name: 'product_reservation_show_detail', defaults: ['review' => false], methods: [
        'GET',
        'POST'
    ])]
    public function productReservation(
        string $token,
        ProductRepository $productRepository,
        Request $request,
        ProductManager $productManager,
        SessionInterface $session,
        EntityManagerInterface $em,
        bool $review = false
    ): Response {
        $user = $this->getUser();
        $lat = $user ? $user->getLat() : $session->get('lat');
        $lon = $user ? $user->getLon() : $session->get('lon');
        $product = $productRepository->findOneBy(['token' => $token]);

        if (!$product) {
            throw $this->createNotFoundException('Ce produit n\'existant pas.');
        }

        $data = [
            'date' => null,
            'quantity' => null
        ];
        $choicesValue = [];
        $quantityLeft = $product->getQuantity() - $product->getQuantityAllReadyReserved();
        if ($quantityLeft) {
            for ($i = 1; $i <= $quantityLeft; $i++) {
                $choicesValue[$i] = $i;
            }
        }
        // on compte une vue
        $product->setNumberView($product->getNumberView() + 1);
        $em->flush();
        // on récupère les tendances selon la catégorie du produit affiché
        $trends = $productRepository->getTrends($lat, $lon, $product->getCategory(), self::MAX_RESULT);

        $options = [
            'quantityLeft' => $quantityLeft,
            'action' => $request->getRequestUri(),
            'choicesValue' => $choicesValue,
            'disabledDates' => $productManager->getDisabledDatesFormProduct($token),
            'token' => $token
        ];

        $form = $this->createForm(ProductReservationType::class, $data, $options);
        $formReview = $this->createForm(
            ProductReviewType::class,
            new Review(),
            ['action' => $this->generateUrl('front_product_rating', ['token' => $token])]
        );

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
            return $this->render(
                'front/product/show_reservation.html.twig',
                compact('product', 'quantityLeft', 'form')
            );
        }

        return $this->render(
            'front/product/show_reservation_detail.html.twig',
            compact('product', 'quantityLeft', 'form', 'trends', 'review', 'formReview')
        );
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
            $options = [
                'action' => $request->getRequestUri(),
                'validation_groups' => ['creation']
            ];
        } else {
            $options = [
                'action' => $request->getRequestUri(),
                'validation_groups' => ['edit']
            ];
        }
        $form = $this->createForm(ProductType::class, $product, $options);

        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $pictureFileDatas = $form->get('uploadedPictures')->getData();
            $productManager->saveOrEditProduct($form->getData(), $pictureFileDatas, (bool)$product->getId());

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
        ProductManager $productManager,
        LoggerInterface $logger
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
            $logger->error('[Product delete] :', ['product' => $product->getId()]);
            $this->addFlash('error', 'Impossible de supprimer le produit');
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
            compact('results')
        );
    }

    #[Route('/produit/image/suppression/{token}/{productToken}', name: 'product_picture_delete', options: ['expose' => true], methods: ['POST'])]
    #[IsGranted('ROLE_SELLER')]
    public function productPictureDelete(
        string $token,
        string $productToken,
        EntityManagerInterface $em,
        ProductManager $productManager
    ): JsonResponse {
        if ($picture = $em->getRepository(Picture::class)->findOneBy(['token' => $token])) {
            $product = $em->getRepository(Product::class)->findOneBy(['token' => $productToken]);
            if ($productManager->deleteProductPicture($product, $picture)) {
                return $this->json(
                    [
                        'success' => true,
                    ]
                );
            }
        }

        return $this->json(['token' => $token], Response::HTTP_UNPROCESSABLE_ENTITY);
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
        $querySearchProducts = $productRepository->searchProducts($filter);
        $data = [
            'sortedBy' => $sortedBy,
            'searchProducts' => $searchProducts
        ];

        $searchProducts = $paginator->paginate(
            $querySearchProducts, /* query NOT result */
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

    #[Route('/avis/{token}', name: 'product_rating')]
    public function rating(string $token, EntityManagerInterface $em, Request $request): Response
    {
        $product = $em->getRepository(Product::class)->findOneBy(['token' => $token]);
        $review = new Review();
        $form = $this->createForm(ProductReviewType::class, $review);
        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $review->setProduct($product);
            $em->persist($review);
            $product->addReview($review);
            $em->flush();

            $this->addFlash('success', 'Enregistrement effectué.');
        } else {
            $this->addFlash('error', 'Une erreur est survenue.');
            return $this->redirectToRoute('front_product_reservation_show_detail', ['token' => $token, 'review' => true]
            );
        }

        return $this->redirectToRoute('front_product_reservation_show_detail', ['token' => $token]);
    }

    #[Route('/check-dates/{startDate}/{token}', name: 'product_check_dates', options: ["expose" => true], methods: ['GET'])]
    public function checkDate(
        \DateTime $startDate,
        string $token,
        EntityManagerInterface $em
    ): JsonResponse {
        $product = $em->getRepository(Product::class)->findOneBy(['token' => $token]);
        $transactionLines = $em->getRepository(TransactionLine::class)->productCheckQuantityAvailable($product, $startDate);
        $totalReserved = 0;
        if ($transactionLines) {
            foreach ($transactionLines as $transactionLine) {
                $totalReserved += $transactionLine->getQuantity();
            }
            $hasReservation = true;
        } else {
            $hasReservation = false;
            $totalReserved = $product->getQuantity();
        }

        if (!$hasReservation) {
            $quantity = $product->getQuantity();
        } else {
            $quantity = $product->getQuantity() - $totalReserved;
        }

        return $this->json(
            [
                'quantity' => $quantity,
            ]
        );
    }
}
