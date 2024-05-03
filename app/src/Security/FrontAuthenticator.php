<?php

namespace App\Security;

use App\Entity\User;
use App\Service\StripeManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\PasswordUpgradeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Authenticator pour le Front Office.
 */
class FrontAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    final public const LOGIN_ROUTE = 'front_login';
    final public const USER_ACCOUNT_ROUTE = 'front_user_account';

    final public const LOGIN_CART_ROUTE = 'front_stripe_payment_user_login';

    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly StripeManager $stripeManager,
        private readonly EntityManagerInterface $em
    ) {}

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('front_login', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
                new PasswordUpgradeBadge($request->request->get('password', '')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // contrôle si on est vendeur et que le compte stripe n'est pas actif
        // alors, on le redirige vers stripe
        $user = $token->getUser();
        $user->setLastDateConnexion(new \DateTime());

        $this->em->flush();
        if (User::ROLE_SELLER === $user->getRole() && !$user->getIsStripeAccountActive()) {
            return new RedirectResponse($this->urlGenerator->generate(self::USER_ACCOUNT_ROUTE));
        }

        // si on se connecte depuis la page de paiement
        if (str_contains($request->headers->get('referer'), 'paiement')) {
            return new RedirectResponse($this->urlGenerator->generate('front_stripe_payment_intent'));
        }

        return new RedirectResponse($this->getLoginUrl($request));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        if ($request->hasSession()) {
            $request->getSession()->getFlashbag()->set('error', 'Compte inconnu ou mot de passe invalide.');
            $request->getSession()->set(SecurityRequestAttributes::AUTHENTICATION_ERROR, $exception);
        }

        return new RedirectResponse($this->getLoginUrl($request));
    }

    protected function getLoginUrl(Request $request): string
    {
        // si on se connecte depuis la page de paiement
        if (str_contains($request->headers->get('referer'), 'paiement')) {
            return $this->urlGenerator->generate(self::LOGIN_CART_ROUTE);
        }

        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
