<?php

namespace App\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

readonly class SiteMapManager
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private RouterInterface $router
    ) {
    }

    /**
     * Retourne la liste des liens du site pour le sitemap.
     */
    public function generateUrls(): array
    {
        $routes = $this->router->getRouteCollection()->all();
        $urls = [];
        foreach ($routes as $name => $route) {
            $requirements = $route->getRequirements();
            if (
                str_starts_with($name, 'front_')
                && !$this->hasRouteParameters($route)
                && (
                    !isset($requirements['_role'])
                    || 'IS_AUTHENTICATED_ANONYMOUSLY' === $requirements['_role']
                    || 'PUBLIC_ACCESS' === $requirements['_role']
                )
                && !$this->getForbiddenRoute($this->urlGenerator->generate($name))
            ) {
                $urls[] = [
                    'loc' => $this->urlGenerator->generate($name, [], UrlGeneratorInterface::ABSOLUTE_URL),
                    'lastmod' => new \DateTime(),
                ];
            }
        }

        return $urls;
    }

    private function hasRouteParameters(Route $route): bool
    {
        foreach ($route->compile()->getVariables() as $variable) {
            if ('_locale' !== $variable) {
                return true;
            }
        }

        return false;
    }

    private function getForbiddenRoute($url): bool
    {
        return in_array(
            $url,
            [
                '/creation-compte-valide',
                '/mon-compte/desactivation',
                '/webhook',
                '/favoris',
                '/edition-compte',
                '/commercial/reauthentification',
                '/commercial/creation-valide',
                '/commercial/edition-compte',
                '/commercial/supprimer-compte',
                '/commercial/retour',
                '/contactez-nous',
                '/admin',
                '/reinitialisation/mot-de-passe/check-email',
                '/valider',
                '/annuler',
                '/paiement-user-creation',
                '/paiement-connexion',
                '/produit-mise-a-jour-liste',
                '/produit-ajout',
                '/panier/mise-a-jour',
                '/panier-widget',
                '/deconnexion',
            ]
        );
    }
}
