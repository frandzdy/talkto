<?php

namespace App\Controller\Front;

use App\Entity\Product;
use App\Entity\Wishlist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class WishlistController extends AbstractController
{
    #[Route('/favoris', name: 'wishlist_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $wishlists = $em->getRepository(Wishlist::class)->findBy(['user' => $this->getUser()->getId()]);

        return $this->render('front/wishlist/index.html.twig', ['wishlists' => $wishlists]);
    }

    #[Route('/favoris/ajout/{token}', name: 'wishlist_add', options: ['expose' => true], methods: ['POST'])]
    public function wishlistAdd(
        string $token,
        EntityManagerInterface $em,
        Request $request
    ): Response {
        $product = $em->getRepository(Product::class)->findOneBy(['token' => $token]);
        $referer = $request->headers->get('referer');
        $wishlist = $em->getRepository(Wishlist::class)->findOneBy(['product' => $product->getToken(), 'user' => $this->getUser()->getToken()]);
        if ($product && !$wishlist) {
            $wishlist = (new Wishlist())
                ->setUser($this->getUser())
                ->setProduct($product)
            ;
            $em->persist($wishlist);

            $em->flush();
            $this->addFlash('success', $wishlist->getProduct()->getTitle().' a été ajouté à vos favoris.');
        } else {
            $this->addFlash('error', $product->getTitle().' existe déjà dans vos favoris.');
        }

        return $this->redirect($referer);
    }

    #[Route('/favoris/supprime/{token}', name: 'wishlist_delete', options: ['expose' => true], methods: ['POST'])]
    public function wishlistDelete(
        string $token,
        EntityManagerInterface $em,
        Request $request
    ): Response {
        $wishlist = $em->getRepository(Wishlist::class)->findOneBy(['token' => $token]);
        $referer = $request->headers->get('referer');

        if ($wishlist->getUser() === $this->getUser()) {
            $this->addFlash('success', $wishlist->getProduct()->getTitle().' a été supprimé de vos favoris.');
            $em->remove($wishlist);
            $em->flush();

            return $this->redirect($referer);
        }

        $this->addFlash('error', $wishlist->getProduct()->getTitle().'Erreur lors de la suppression.');

        return $this->redirect($referer);
    }
}
