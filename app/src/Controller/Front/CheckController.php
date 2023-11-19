<?php

namespace App\Controller\Front;


use App\Entity\Checkin;
use App\Entity\TransactionLine;
use App\Enum\CheckinStatus;
use App\Form\Front\CheckinType;
use App\Service\CheckManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CheckController extends AbstractController
{
    /**
     * Gère la création du check in ou out
     */
    #[Route('/check/{type}/{token}', name: 'check_create', requirements: ['type' => 'in|out'], methods: ['GET', 'POST'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function check(
        string $type,
        string $token,
        EntityManagerInterface $em,
        Request $request,
        CheckManager $checkManager
    ): Response {
        $transactionLine = $em->getRepository(TransactionLine::class)->findOneBy(['token' => $token]);
        $hasAllReadyDoneCheckin = $em->getRepository(Checkin::class)->findOneBy(
            [
                'transactionLine' => $transactionLine->getId(),
                'status' => $type === 'in' ? CheckinStatus::IN->value : CheckinStatus::OUT->value
            ]
        );

        if (!$hasAllReadyDoneCheckin) {
            $checkin = $checkManager->createCheckin($this->getUser(), $type, $transactionLine);
        } else {
            $checkin = $hasAllReadyDoneCheckin;
        }
        $form = $this->createForm(CheckinType::class, $checkin, ['action' => $request->getUri()]);

        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {

            $checkManager->saveCheckin($checkin, $checkin->uploadedPictures);
            $this->addFlash('success', 'Check' . $type . ' enregistré !');

            return $this->json(
                [
                    'success' => true,
                    'redirectUrl' => $this->generateUrl('front_user_account')
                ]
            );
        }

        return $this->render('front/check/create.html.twig', compact('form'));
    }
}
