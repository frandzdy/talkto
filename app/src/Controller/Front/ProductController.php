<?php

namespace App\Controller\Front;

use App\Entity\Picture;
use App\Entity\Product;
use App\Entity\User;
use App\Enum\ProductCategory;
use App\Form\ProductType;
use App\Form\ProductReservationType;
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
    #[Route('/produit/{token}', name: 'product_show', methods: ['GET'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function show(string $token, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findOneBy(['token' => $token]);

        return $this->render('front/product/show.html.twig', compact('product'));
    }

    /**
     * Affiche la page détails d'un produit
     *
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
     *
     * @param string $token
     * @param ProductRepository $productRepository
     * @param Request $request
     * @param $productManager $productManager
     * @return Response
     */
    #[Route('/produit-reservation/{token}', name: 'product_reservation', methods: ['GET', 'POST'])]
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

            return $this->json(
                [
                    'success' => true,
                    'redirectUrl' => $this->generateUrl('front_home')
                ]
            );
        }

        return $this->render('front/product/show_reservation.html.twig', compact('product', 'quantityLeft', 'form'));
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
                    'callback' => 'onProductChange'
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
    #[IsGranted("ROLE_SELLER")]
    public function productUpdateList(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy(['user' => $this->getUser()]);

        return $this->render(
            'front/user/partials/_list.html.twig',
            compact('products')
        );
    }

    #[Route('/produit/image/suppression/{token}', name: 'product_picture_delete', methods: ['POST'])]
    #[IsGranted('ROLE_SELLER')]
    public function productPictureDelete(string $token, EntityManagerInterface $em): JsonResponse
    {
        if ($picture = $em->getRepository(Picture::class)->findOneBy(['token'])) {
            $em->remove($picture);
            $em->flush();
            return $this->json(['token' => $token]);
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
        PaginatorInterface $paginator
    ): Response {
        $amount = $request->query->get('amount', '0-300');
        $distance = $request->query->get('distance', '0-2');
        $sortedBy = $request->query->getInt('sortedBy', 1);
        $filter = [
            'startAmount' => explode('-', $amount)[0] ?? 0,
            'endAmount' => explode('-', $amount)[1] ?? 300,
            'startDistance' => explode('-', $distance)[0] ?? 0,
            'endDistance' => explode('-', $distance)[1] ?? 2,
            'sortedBy' => $sortedBy,
            'category' => $productCategory->value
        ];
        $queryProducts = $em->getRepository(Product::class)->getFilteredProducts($this->getUser(), $filter);

        $products = $paginator->paginate(
            $queryProducts, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            20 /*limit per page*/,
            ['wrap-queries' => true]
        );

        if ($request->getMethod() === "POST") {

            return $this->json(
                [
                    'response' => $this->renderView(
                        'front/product/category.html.twig',
                        [
                            'pagination' => $products,
                            'productCategory' => $productCategory
                        ]
                    )
                ]
            );
        }

        return $this->render(
            'front/product/category.html.twig',
            [
                'pagination' => $products,
                'productCategory' => $productCategory
            ]
        );
    }
}
