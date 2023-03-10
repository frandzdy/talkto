<?php
    
    namespace App\Controller;
    
    use App\Model\ContactModel;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\UX\Turbo\Stream\TurboStreamResponse;

    class AboutUsController extends AbstractController
    {
        #[Route('/à-propos', name: 'app_about_us', methods: ['GET'])]
        public function aboutUs(
        ): Response|string {

            return $this->render('aboutus/index.html.twig');
        }
    }
