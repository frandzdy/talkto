<?php

namespace App\Controller\Front;

use App\Entity\Checkin;
use App\Entity\Reservation;
use App\Entity\TransactionLine;
use App\Enum\CheckinType as CheckinTypeEnum;
use App\Form\Front\CheckinType;
use App\Service\CheckManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CheckController extends AbstractController
{
    /**
     * Gère la création du check in ou out.
     */
    #[Route('/check/{type}/{token}', name: 'check_create', requirements: ['type' => 'in|out'], methods: [
        'GET',
        'POST',
    ])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function check(
        string $type,
        string $token,
        EntityManagerInterface $em,
        Request $request,
        CheckManager $checkManager
    ): Response {
        $transactionLine = $em->getRepository(TransactionLine::class)->findOneBy(['token' => $token]);
        $reservation = $em->getRepository(Reservation::class)->findOneBy(
            ['transaction' => $transactionLine->getTransaction()]
        );
        $hasAllReadyDoneCheckin = $em->getRepository(Checkin::class)->findOneBy(
            [
                'transactionLine' => $transactionLine->getId(),
                'status' => 'in' === $type ? CheckinTypeEnum::IN->value : CheckinTypeEnum::OUT->value,
            ]
        );

        if (null === $hasAllReadyDoneCheckin) {
            $checkin = $checkManager->createCheckin($this->getUser(), $type, $transactionLine);
        } else {
            $checkin = $hasAllReadyDoneCheckin;
        }

        $form = $this->createForm(CheckinType::class, $checkin, ['action' => $request->getUri()]);
        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $hasclaim = $checkManager->saveCheckin($checkin, $reservation, $checkin->uploadedPictures);
            $this->addFlash('success', 'Check'.$type.' enregistré.');
            if ($hasclaim) {
                $this->addFlash('success', 'Un ticket a été ouvert pour analyse.');
            }

            return $this->json(
                [
                    'success' => true,
                    'reload' => true,
                    'redirectUrl' => $this->generateUrl('front_user_account'),
                ]
            );
        }

        return $this->render('front/check/create.html.twig', ['form' => $form]);
    }
}
