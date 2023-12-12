<?php

namespace App\Controller\Back;

use App\Entity\TransactionLine;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/', name: 'home_', methods: ['GET', 'POST'])]
class HomeController extends AbstractController
{
    #[Route(path: '', name: 'dashboard', methods: ['GET', 'POST'])]
    public function dashboard(EntityManagerInterface $em): Response
    {
        $stats = [];
        $allProfits = $em->getRepository(TransactionLine::class)->getStatTransactionLine();
        $dayProfits = $em->getRepository(TransactionLine::class)->getStatTransactionLine(new \DateTime());
        $nbTransaction = $em->getRepository(TransactionLine::class)->findAll();
        $daySellers = $em->getRepository(User::class)
            ->findBy(
                [
                    'createdAt' => new \DateTime(),
                    'roles' => User::ROLE_SELLER
                ]
            );
        $allSellers = $em->getRepository(User::class)
            ->findBy(
                [
                    'roles' => User::ROLE_SELLER
                ]
            );
        $dayGuesses = $em->getRepository(User::class)
            ->findBy(
                [
                    'createdAt' => new \DateTime(),
                    'roles' => User::ROLE_GUESS
                ]
            );
        $allGuesses = $em->getRepository(User::class)
            ->findBy(
                [
                    'roles' => User::ROLE_GUESS
                ]
            );
        $dayRentes = $em->getRepository(User::class)
            ->findBy(
                [
                    'createdAt' => new \DateTime(),
                    'roles' => User::ROLE_USER
                ]
            );
        $allRentes = $em->getRepository(User::class)
            ->findBy(
                [
                    'roles' => User::ROLE_USER
                ]
            );
        $stats = [
            'allProfits' => $allProfits[0]['profit'] ? number_format($allProfits[0]['profit'] / 100, 2, ',', ' ') : 0,
            'allCa' => $allProfits[0]['ca'] ? number_format($allProfits[0]['ca'] / 100, 2, ',', ' ') : 0,
            'dayProfits' => $dayProfits[0]['profit'] ? number_format($dayProfits[0]['profit'] / 100, 2, ',', ' ') : 0,
            'dayCa' => $dayProfits[0]['ca'] ? number_format($dayProfits[0]['ca'] / 100, 2, ',', ' ') : 0,
            'nbTransaction' => \count($nbTransaction),
            'daySellers' => \count($daySellers),
            'allSellers' => \count($allSellers),
            'dayGuesses' => \count($dayGuesses),
            'allGuesses' => \count($allGuesses),
            'dayRentes' => \count($dayRentes),
            'allRentes' => \count($allRentes)
        ];
        dump($stats);

        return $this->render('back/home/dashboard.html.twig', compact('stats'));
    }
}
