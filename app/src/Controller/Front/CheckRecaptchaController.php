<?php

namespace App\Controller\Front;

use App\Service\ContactManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;


class CheckRecaptchaController extends AbstractController
{
    #[Route('/check-recaptcha/{token}', name: 'recaptcha_check', options: ["expose" => true], methods: ['POST'])]
    public function checkRecaptcha(string $token, string $googleRecaptchaSkey, ContactManager $contactManager): JsonResponse
    {
        return new JsonResponse(['response' => $contactManager->checkForm($googleRecaptchaSkey, $token)], 200);
    }
}