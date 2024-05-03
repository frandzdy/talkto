<?php

namespace App\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Exception lancée au login pour l'affichage de la popin de siret.
 */
class LoginSiretException extends AuthenticationException {}
