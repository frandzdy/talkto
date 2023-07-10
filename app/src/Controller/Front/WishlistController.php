<?php

namespace App\Controller\Front;

use App\Entity\Product;
use App\Entity\Wishlist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("IS_AUTHENTICATED_FULLY")]
class WishlistController extends AbstractController
{
    #[Route('/liste-envie', name: 'wishlist_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $wishlists = $em->getRepository(Wishlist::class)->findBy(['user' => $this->getUser()->getId()]);

        return $this->render('front/wishlist/index.html.twig', compact('wishlists'));
    }

    #[Route('/liste-envie/ajout/{token}', name: 'wishlist_add', options: ['expose' => true], methods: ['POST'])]
    public function wishlistAdd(string $token, EntityManagerInterface $em, Request $request, RouterInterface $router): Response
    {
        $product = $em->getRepository(Product::class)->findOneBy(['token' => $token]);
        $referer = $this->generateUrl('front_home');
        foreach ($router->getRouteCollection() as $name => $route) {
            if (str_contains($route->getPath(), $request->headers->get('referer'))) {
                $referer = $request->headers->get('referer');
            }
        }

        if ($product) {
            $wishlist = (new Wishlist())
                ->setUser($this->getUser())
                ->setProduct($product);

            $em->persist($wishlist);
            $em->flush();
            // réucpère le referer
            $this->addFlash('success', $wishlist->getProduct()->getTitle() . ' a été ajouté.');

            return $this->redirect($referer);
        }
        $this->addFlash('error', 'Erreur lors de l\'ajout.');

        return $this->redirect($referer);
    }

    #[Route('/liste-envie/supprime/{token}', name: 'wishlist_delete', options: ['expose' => true], methods: ['POST'])]
    public function wishlistDelete(string $token, EntityManagerInterface $em, Request $request, RouterInterface $router): Response
    {
        $wishlist = $em->getRepository(Wishlist::class)->findOneBy(['token' => $token]);
        $referer = $this->generateUrl('front_home');
        foreach ($router->getRouteCollection() as $name => $route) {
            if (str_contains($route->getPath(), $request->headers->get('referer'))) {
                $referer = $request->headers->get('referer');
            }
        }
        if ($wishlist->getUser() === $this->getUser()) {
            $this->addFlash('success', $wishlist->getProduct()->getTitle() . ' a été supprimé.');
            $em->remove($wishlist);
            $em->flush();
            // réucpère le referer

            return $this->redirect($referer);
        }
        $this->addFlash('error', $wishlist->getProduct()->getTitle() . 'Erreur lors de la suppression.');
        // réucpère le referer

        return $this->redirect($referer);
    }
}
