<?php

namespace App\Controller\Back;

use App\Entity\HomePage;
use App\Form\Back\HomePageType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/homepage', name: 'homepage_')]
class HomePageController extends AbstractController
{
    #[Route(path: '/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $homePage = $em->getRepository(HomePage::class)->findOneBy(['id' => 1]);

        if (!$homePage) {
            $homePage = (new HomePage())->setLabel('Homepage création');
            $em->persist($homePage);
            $em->flush();
        }

        $form = $this->createForm(HomePageType::class, $homePage);

        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
        $this->addFlash('success', 'Enregistrement effectué.');
        dump($homePage);
        $em->flush();

        return $this->redirectToRoute('back_homepage_edit');
    }

        return $this->render('back/homepage/edit.html.twig', ['form' => $form]);
    }
}
