<?php

namespace App\Controller\Back;

use App\Entity\HomePage;
use App\Form\Back\HomePageType;
use App\Service\HomePageManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/homepage', name: 'homepage_')]
class HomePageController extends AbstractController
{
    #[Route(path: '/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $em, HomePageManager $homePageManager): Response
    {
        $homePage = $em->getRepository(HomePage::class)->findOneBy(['id' => 1]);

        if (null === $homePage) {
            $homePage = (new HomePage())->setTitle('Homepage création');
            $em->persist($homePage);
            $em->flush();
        }

        $form = $this->createForm(HomePageType::class, $homePage);

        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $homePage = $form->getData();
            $homePageManager->saveHomePage($homePage);
            $this->addFlash('success', 'Enregistrement effectué.');
            $em->flush();

            return $this->redirectToRoute('back_homepage_edit');
        }

        return $this->render('back/homepage/edit.html.twig', ['form' => $form]);
    }
}
