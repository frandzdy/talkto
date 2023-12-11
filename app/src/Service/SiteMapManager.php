<?php

namespace App\Service;

use App\Entity\Qualification;
use App\Entity\WebsiteMenuCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Génére le site map de l'application
 */
class SiteMapManager
{
    /**
     * SiteMapManager constructor.
     */
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private RouterInterface $router
    ) {}

    /**
     * Retourne la liste des liens du site pour le sitemap
     */
    public function generateUrls(): array
    {
        $routes = $this->router->getRouteCollection()->all();

        $urls = [];
        foreach ($routes as $name => $route) {
            if (str_starts_with($name, 'front_') && !$this->hasRouteParameters($route)) {
                $urls[] = [
                    'loc' => $this->urlGenerator->generate($name, [], UrlGeneratorInterface::ABSOLUTE_URL),
                    'lastmod' => new \DateTime(),
                ];
            }
        }

        return $urls;
    }

    private function hasRouteParameters($route): bool
    {
        foreach ($route->compile()->getVariables() as $variable) {
            if ($variable !== '_locale') {
                return true;
            }
        }

        return false;
    }
}
