<?php

namespace App\Controller\Back;

use App\Entity\Picture;
use App\Entity\Product;
use App\Enum\ProductStatus;
use App\Exporter\ProductExporter;
use App\Form\Back\ProductFilterType;
use App\Form\Back\ProductType;
use App\Repository\ProductRepository;
use App\Service\MailerManager;
use App\Service\ProductManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Gestion des produits.
 */
#[Route(path: '/products', name: 'product_')]
class ProductController extends AbstractController
{
    final public const PRODUCTS_PER_PAGE = 50;

    final public const PRODUCTS_TERM_FILTER = 'product.filter';

    /**
     * Liste des produits.
     */
    #[Route(path: '/', name: 'index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        ProductRepository $productRepository,
        PaginatorInterface $paginator
    ): Response {
        $filtersFormSession = $request->getSession()->get(self::PRODUCTS_TERM_FILTER, null);
        if (!$filtersFormSession) {
            $filters = [
                'term' => $request->query->get('term', ''),
                'status' => $request->query->getEnum('status', ProductStatus::class, ProductStatus::WAITING),
            ];
        } else {
            $filters = $filtersFormSession;
        }

        $page = $request->query->getInt('page', 0) > 0 ? $request->query->getInt('page') : 1;

        $filterForm = $this->createForm(ProductFilterType::class, $filters);
        if ($filterForm->handleRequest($request)->isSubmitted() && $filterForm->isValid()) {
            $filters = $filterForm->getData() ?? [];
            $request->getSession()->set(self::PRODUCTS_TERM_FILTER, $filters);
        }

        $query = $productRepository->buildSearchQuery($filters);

        $paginator = $paginator->paginate(
            $query,
            $page,
            self::PRODUCTS_PER_PAGE,
            [
                PaginatorInterface::DEFAULT_SORT_FIELD_NAME => 'p.status',
                PaginatorInterface::DEFAULT_SORT_DIRECTION => 'ASC',
                PaginatorInterface::DISTINCT => false,
            ]
        );

        return $this->render(
            'back/product/index.html.twig',
            [
                'products' => $paginator,
                'filterForm' => $filterForm->createView(),
            ]
        );
    }

    /**
     * Affichage d'une fiche produit.
     */
    #[Route(path: '/{id<\d+>}', name: 'show', methods: ['GET', 'POST'])]
    public function show(
        Product $product,
        Request $request,
        EntityManagerInterface $em,
        MailerManager $mailerManager
    ): Response {
        $form = $this->createForm(ProductType::class, $product);

        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Enregistrement effectué.');
            $em->flush();

            if (ProductStatus::VALIDATE === $product->getStatus()) {
                $mailerManager->sendMailNotification(
                    $product->getAuthor()->getEmail(),
                    'emails/product_validation.html.twig',
                    [
                        'product' => $product,
                        'user' => $product->getAuthor(),
                    ]
                );
            } else {
                $mailerManager->sendMailNotification(
                    $product->getAuthor()->getEmail(),
                    'emails/product_rejected.html.twig',
                    [
                        'product' => $product,
                        'user' => $product->getAuthor(),
                        'responseRejected' => $product->responseRejected,
                    ]
                );
            }

            return $this->redirectToRoute('back_product_show', ['id' => $product->getId()]);
        }

        return $this->render('back/product/show.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    /**
     * Génère un export de tous les bailleurs.
     */
    #[Route(path: '/extract-product/{typeFile}', name: 'extract', requirements: ['typeFile' => '(csv|xlsx)'], defaults: ['typeFile' => 'xlsx'], methods: ['GET'])]
    public function export(
        ProductRepository $productRepository,
        string $typeFile,
        ProductExporter $productExporter
    ): NotFoundHttpException|Response {
        $products = $productRepository->findAll();
        $callable = 'exportAs'.strtoupper($typeFile);

        if (is_callable($callable, true, $callableNameFunction)) {
            $result = $productExporter->{$callableNameFunction}($products);
        } else {
            throw $this->createNotFoundException('Exporter Method not found');
        }

        return new Response(
            $result['file'],
            200,
            [
                'Content-Type' => $result['contentType'].'; charset=windows-1251',
                'Content-Disposition' => 'attachment; filename="export_produits.'.$typeFile.'"',
            ]
        );
    }

    #[Route('/image/suppression/{id}/{product}', name: 'picture_delete', options: ['expose' => true], methods: ['POST'])]
    #[IsGranted('ROLE_CONTRIBUTOR')]
    public function productPictureDelete(
        Picture $picture,
        Product $product,
        ProductManager $productManager,
        MailerManager $mailerManager
    ): JsonResponse {
        if ($productManager->deleteProductPicture($product, $picture)) {
            $this->addFlash('success', 'Suppression effectué.');
            $mailerManager->sendMailNotification(
                $product->getAuthor()->getEmail(),
                'emails/product_picture_deleted.html.twig',
                [
                    'user' => $product->getAuthor(),
                    'product' => $product,
                ]
            );

            return $this->json(
                [
                    'success' => true,
                    'redirectUrl' => $this->generateUrl('back_product_show', ['id' => $product->getId()]),
                ]
            );
        }

        return $this->json(['success' => false], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
