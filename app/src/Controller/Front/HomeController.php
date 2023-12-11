<?php

namespace App\Controller\Front;

use App\Entity\HomePage;
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
    public const HOME_PAGE_ID = 1;

    #[Route('/', name: 'home')]
    public function index(EntityManagerInterface $em, SessionInterface $session): Response
    {
        $user = $this->getUser();
        $lat = $user ? $user->getLat() : $session->get('lat');
        $lon = $user ? $user->getLon() : $session->get('lon');
        // dans la homepage on récupère Slider, sous slide et bande bas
        $homePage = $em->getRepository(HomePage::class)->findOneBy(['id' => self::HOME_PAGE_ID]);
        $trends = $em->getRepository(Product::class)->getTrends($lat, $lon, maxResult: 6);
        $latestProducts = $em->getRepository(Product::class)->getLatestProducts($lat, $lon);
        $topSales = $em->getRepository(TransactionLine::class)->getTopSales($lat, $lon);

        return $this->render('front/home/index.html.twig', compact('homePage', 'trends', 'latestProducts', 'topSales'));
    }
    
    /**
     * Génère le sitemap du site.
     */
    #[Route('//sitemap.{_format}', name: 'sitemap', requirements: ['_format'=> 'xml'])]
    public function siteMap()
    {
        return $this->render('home/sitemap.xml.twig');
    }
}
