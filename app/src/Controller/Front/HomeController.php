<?php

namespace App\Controller\Front;

use App\Entity\Product;
use App\Entity\TransactionLine;
use App\Enum\ProductCategory;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(EntityManagerInterface $em, SessionInterface $session): Response
    {
        $user = $this->getUser();
        $lat = $user ? $user->getLat() : $session->get('lat');
        $lon = $user ? $user->getLon() : $session->get('lon');

        $em->getRepository(Product::class)->getTrends($lat, $lon);
        $em->getRepository(Product::class)->getLastestProduct($lat, $lon);
        $em->getRepository(TransactionLine::class)->getTopSales($lat, $lon);
        return $this->render('front/home/index.html.twig');
    }
    
    /**
     * Génère le sitemap du site.
     *
     * @Route("/sitemap.{_format}", name="sitemap", requirements={"_format" = "xml"})
     */
    public function siteMap()
    {
        return $this->render('home/sitemap.xml.twig');
    }
}
