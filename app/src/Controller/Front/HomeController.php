<?php

namespace App\Controller\Front;

use App\Entity\HomePage;
use App\Entity\Product;
use App\Entity\TransactionLine;
use App\Service\SiteMapManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Cache(maxage: '3600')]
class HomeController extends AbstractController
{
    final public const HOME_PAGE_ID = 1;

    #[Route('/', name: 'home')]
    public function index(EntityManagerInterface $em, SessionInterface $session): Response
    {
        $user = $this->getUser();
        $lat = $user instanceof UserInterface ? $user->getLat() : $session->get('lat');
        $lon = $user instanceof UserInterface ? $user->getLon() : $session->get('lon');
        // Dans la homepage, on récupère Slider, sous slide et bande bas
        $homePage = $em->getRepository(HomePage::class)->getHomePage(self::HOME_PAGE_ID);
        $trends = $em->getRepository(Product::class)->getTrends($lat, $lon, maxResult: 6);
        $latestProducts = $em->getRepository(Product::class)->getLatestProducts($lat, $lon);
        $topSales = $em->getRepository(TransactionLine::class)->getTopSales($lat, $lon);

        return $this->render(
            'front/home/index.html.twig',
            ['homePage' => $homePage, 'trends' => $trends, 'latestProducts' => $latestProducts, 'topSales' => $topSales]
        );
    }

    /**
     * Génère le sitemap du site.
     */
    #[Route('/sitemap.{_format}', name: 'sitemap', requirements: ['_format' => 'xml'])]
    public function siteMap(SiteMapManager $siteMapManager): Response
    {
        return $this->render(
            'front/home/sitemap.xml.twig',
            ['urls' => $siteMapManager->generateUrls()]
        );
    }
}
