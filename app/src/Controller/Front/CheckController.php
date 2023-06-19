<?php

namespace App\Controller\Front;

use App\Entity\Check;
use App\Entity\TransactionLine;
use App\Enum\CheckStatus;
use App\Form\CheckType;
use App\Repository\CheckRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CheckController extends AbstractController
{
    #[Route('/check/{type}/{token}', name: 'check_create', requirements: ['type' => 'in|out'], methods: ['GET'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function check(
        string $type,
        string $token,
        EntityManagerInterface $em,
        Request $request
    ): Response
    {
        $transactionLine = $em->getRepository(TransactionLine::class)->findOneBy(['token' => $token]);
        $check = (new Check())
        ->setStatus(CheckStatus::IN)
            ->setTransactionLine($transactionLine)
        ;
        $form = $this->createForm(CheckType::class, $check);

        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {

        }

        return $this->render('front/check/create.html.twig', compact('form'));
    }
}
