<?php
    
    namespace App\Controller;
    
    use App\Service\ContactManager;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    
    class RecaptchaController extends AbstractController
    {
        #[Route('/check-recaptcha/{token}', name: 'app_contact_recaptcha_check', methods: ['POST'], options: ["expose" => true])]
        public function checkRecaptcha(string $token, string $googleRecaptchaSkey, ContactManager $contactManager): Response
        {
            return new JsonResponse(['response' => $contactManager->checkForm($googleRecaptchaSkey, $token)], 200);
        }
    }
