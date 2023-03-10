<?php

namespace App\Controller;

use App\Form\SignalType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/signaler", name: "app_")]
#[IsGranted("IS_AUTHENTICATED_FULLY")]
class SignalController extends AbstractController
{
    #[Route("/{token}", name: "signal")]
    #[Route("/chat/{token}", name: "signal_chat", options: ['expose'=> true])]
    public function signal(string $token, UserRepository $userRepository, Request $request) : Response
    {
        $user = $userRepository->findOneBy(['token' => $token]);
        $options = [
            'action' => $request->attributes->get('_route') === "app_signal" ?
                $this->generateUrl('app_signal', ['token' => $user->getToken()]) :
                $this->generateUrl('app_signal_chat', ['token' => $user->getToken()])
        ];
        $form = $this->createForm(SignalType::class, ['nom' => $user->getLastname()], $options);
        
        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', "Modifications enregistrées avec succès");
            return $this->json(
                [
                    'success' => true,
                    'redirectUrl' => $request->attributes->get('_route') === "app_signal" ? $this->generateUrl('app_match_list') : $this->generateUrl('chat_show')
                ],
                Response::HTTP_OK
            );
        }
        
        return $this->render('signal/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
