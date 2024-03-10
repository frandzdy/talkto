<?php

namespace App\Controller\Front;

use App\Form\Front\SellerType;
use App\Form\Front\UserType;
use App\Security\FrontAuthenticator;
use App\Service\MailerManager;
use App\Service\StripeManager;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SellerController extends AbstractController
{
    #[Route('/commercial/creation-compte', name: 'seller_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        UserManager $userManager,
        MailerManager $mailer,
        StripeManager $stripeManager,
        Security $security
    ): Response {
        if ($this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('front_user_account');
        }

        $user = $userManager->createUser(2);

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFileData = $form->get('uploadPicture')->getData();

            $userManager->saveOrEditUser($form->getData(), $pictureFileData);
            // changer vers une route de success de création
            $mailer->sendMailNotification(
                $user->getEmail(),
                'emails/create_lessor.html.twig',
                [
                    'user' => $user,
                ]
            );
            $userManager->saveUser();
            $security->login($user, FrontAuthenticator::class, 'front');

            return $this->redirect($stripeManager->createAccountLink($user)->url);
        }

        return $this->render('front/user/seller/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/commercial/edition-compte', name: 'seller_edit', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function edit(
        Request $request,
        UserManager $userManager
    ): Response {
        $user = $this->getUser();
        $form = $this->createForm(SellerType::class, $user, ['action' => $request->getRequestUri(), 'edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFileData = $form->get('uploadPicture')->getData();

            $userManager->saveOrEditUser($form->getData(), $pictureFileData);
            $this->addFlash('success', 'Enregistrement effectué.');

            return $this->json(
                [
                    'success' => true,
                    'redirectUrl' => $this->generateUrl('front_seller_edit'),
                ],
                Response::HTTP_OK
            );
        }

        return $this->render('front/user/seller/_form.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * Supprime le compte d'un vendeur.
     */
    #[Route('/commercial/supprimer-compte', name: 'seller_delete', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function delete(Request $request, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($this->getUser());
        $entityManager->flush();

        return $this->redirectToRoute('login', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Affiche le message de succès pour la création d'un compte.
     */
    #[Route('/commercial/creation-valide', name: 'seller_success_creation', methods: ['GET'])]
    public function successCreation(): Response
    {
        return $this->render('front/user/seller/creation-success.html.twig');
    }
}
