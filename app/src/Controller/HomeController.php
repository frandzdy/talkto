<?php

namespace App\Controller;

use App\Form\LoginType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request, RequestStack $requestStack, UserRepository $userRepository): Response
    {
        if ($this->getUser()) {
            
            return $this->redirectToRoute('user_edit');
        }
        $data = null;
        $form = $this->createForm(LoginType::class, $data, ['method' => 'POST']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $requestStack->getSession()->set('email', $data['email']);
         
            return $this->redirectToRoute('user_new', ['type' => 'free'],Response::HTTP_SEE_OTHER);
        }
        
        return $this->renderForm('home/index.html.twig', [
            'form' => $form,
            'countUser' => \count($userRepository->findAll())
        ]);
    }
    
    /**
     * Génère le sitemap du site.
     *
     * @Route("/sitemap.{_format}", name="sitemap", requirements={"_format" = "xml"})
     */
    public function siteMap()
    {
        return $this->render('home/sitemap.xml.twig');
    }
}
