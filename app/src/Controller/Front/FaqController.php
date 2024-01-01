<?php

namespace App\Controller\Front;

use App\Form\Front\ContactType;
use App\Model\ContactModel;
use App\Service\ContactManager;
use App\Service\MailerManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Cache(maxage: '3600')]
class FaqController extends AbstractController
{
    #[Route('/faq', name: 'faq', methods: ['GET'])]
    public function faq(): Response
    {
        return $this->render('front/faq/index.html.twig');
    }
}
