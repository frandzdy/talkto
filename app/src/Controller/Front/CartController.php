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
    public function widget(SessionInterface $session): Response
    {
        $carts = $session->get('cart', null);

        return $this->render('front/car/cart-widget.html.twig', compact('carts'));
    }

    #[Route('/panier', name: 'cart_index', methods: ['GET'])]
    public function index(SessionInterface $session): Response
    {
        $carts = $session->get('cart', null);

        return $this->render('front/car/cart.html.twig', compact('carts'));
    }
}
