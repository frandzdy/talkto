<?php

namespace App\Controller\Back;

use App\Entity\Transaction;
use App\Entity\TransactionLine;
use App\Entity\User;
use App\Enum\TransactionStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Clock\DatePoint;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/', name: 'home_', methods: ['GET', 'POST'])]
class HomeController extends AbstractController
{
    #[Route(path: '', name: 'dashboard', methods: ['GET', 'POST'])]
    public function dashboard(EntityManagerInterface $em): Response
    {
        $allProfits = $em->getRepository(TransactionLine::class)->getStatTransactionLine();
        $dayProfits = $em->getRepository(TransactionLine::class)->getStatTransactionLine(new DatePoint());
        $nbTransaction = $em->getRepository(Transaction::class)->findBy(['status' => TransactionStatus::VALIDATE]);

        $allSellers = $em->getRepository(User::class)->statsUsers(User::ROLE_SELLER);
        $daySellers = $em->getRepository(User::class)->statsUsers(User::ROLE_SELLER, new DatePoint());

        $allGuesses = $em->getRepository(User::class)->statsUsers(User::ROLE_GUESS);
        $dayGuesses = $em->getRepository(User::class)->statsUsers(User::ROLE_GUESS, new DatePoint());

        $allRentes = $em->getRepository(User::class)->statsUsers(User::ROLE_USER);
        $dayRentes = $em->getRepository(User::class)->statsUsers(User::ROLE_USER, new DatePoint());

        $stats = [
            'allProfits' => current($allProfits)['profit'] ? number_format(
                current($allProfits)['profit'] / 100,
                2,
                ',',
                ' '
            ) : 0,
            'allCa' => current($allProfits)['ca'] ? number_format(current($allProfits)['ca'] / 100, 2, ',', ' ') : 0,
            'dayProfits' => current($dayProfits)['profit'] ? number_format(
                current($dayProfits)['profit'] / 100,
                2,
                ',',
                ' '
            ) : 0,
            'dayCa' => current($dayProfits)['ca'] ? number_format(current($dayProfits)['ca'] / 100, 2, ',', ' ') : 0,
            'nbTransaction' => \count($nbTransaction),
            'daySellers' => current($daySellers)['nbUsers'],
            'allSellers' => current($allSellers)['nbUsers'],
            'dayGuesses' => current($dayGuesses)['nbUsers'],
            'allGuesses' => current($allGuesses)['nbUsers'],
            'dayRentes' => current($dayRentes)['nbUsers'],
            'allRentes' => current($allRentes)['nbUsers'],
        ];

        return $this->render('back/home/dashboard.html.twig', ['stats' => $stats]);
    }
}
