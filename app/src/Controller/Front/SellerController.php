<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Form\SellerType;
use App\Form\UserType;
use App\Security\FrontAuthenticator;
use App\Service\MailerManager;
use App\Service\StripeManager;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class SellerController extends AbstractController
{
    #[Route('/mon-compte-commercial', name: 'seller_account', methods: ['GET'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function account(): Response
    {
        return $this->render('front/user/seller/account.html.twig', ['user' => $this->getUser()]);
    }

    #[Route('/creation-compte-commercial', name: 'seller_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        UserManager $userManager,
        MailerManager $mailer,
        StripeManager $stripeManager,
        UserAuthenticatorInterface $authenticator,
        FrontAuthenticator $frontAuthenticator
    ): Response {
        if (!is_null($this->getUser())) {

            return $this->redirectToRoute('front_seller_edit');
        }
        $user = $userManager->createUser(2);

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFileData = $form->get('picture')->getData();

            $userManager->saveOrEditUser($form->getData(), $pictureFileData);
            // changer vers une route de success de création
            $mailer->sendMailNotification(
                $user->getEmail(),
                'front/emails/create_seller.html.twig',
                [
                    'user' => $user,
                ]
            );
            $userManager->saveUser();
            $authenticator->authenticateUser($user, $frontAuthenticator, $request);

            return $this->redirect($stripeManager->createAccountLink($user)->url);
        }

        return $this->render('front/user/seller/edit.html.twig', [
            'user' => $user,
            'form' => $form
        ]);
    }

    #[Route('/compte-commercial/edition', name: 'seller_edit', methods: ['GET', 'POST'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function edit(
        Request     $request,
        UserManager $userManager
    ): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(SellerType::class, $user, ['action' => $request->getRequestUri()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFileData = $form->get('picture')->getData();

            $userManager->saveOrEditUser($form->getData(), $pictureFileData, true);
            $this->addFlash('success', 'Enregistrement effectué.');

            return $this->json(
                [
                    'success' => true,
                    'redirectUrl' => $this->generateUrl('front_seller_edit')
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
     * Supprime le compte d'utilisateur connecté
     */
    #[Route('/mon-compte-commercial/supprimer', name: 'user_delete', methods: ['POST'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function delete(Request $request, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($this->getUser());
        $entityManager->flush();

        return $this->redirectToRoute('login', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Affiche le message de succès pour la créatin d'un compte
     */
    #[Route('/creation-compte-commercial-valide', name: 'seller_success_creation', methods: ['GET'])]
    public function userSuccessCreation(): Response
    {

        return $this->render('front/user/seller/creation-success.html.twig');
    }
}
