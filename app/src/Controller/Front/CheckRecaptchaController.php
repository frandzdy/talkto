<?php

namespace App\Controller\Front;

use App\Service\RecaptchaManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class CheckRecaptchaController extends AbstractController
{
    /**
     * Appel l'api recaptcha de Google pour vérifier si on est en face d'un robot.
     */
    #[Route('/check-recaptcha/{token}', name: 'recaptcha_check', options: ['expose' => true], methods: ['POST'])]
    public function checkRecaptcha(
        string $token,
        string $googleRecaptchaSkey,
        RecaptchaManager $recaptchaManager
    ): JsonResponse {
        return new JsonResponse(['response' => $recaptchaManager->checkForm($googleRecaptchaSkey, $token)], 200);
    }
}
