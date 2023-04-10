<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\MailerManager;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

class UserController extends AbstractController
{
    #[Route('/profile/{tokenId}', name: 'user_show_friend', methods: ['GET'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function profile($tokenId, UserRepository $userRepository): Response
    {
        // on check quand même que le token fait parti des tokens autorisés
        $user = $userRepository->findOneBy(['token' => $tokenId]);
        return $this->render('user/show.html.twig', ['user' => $user]);
    }

    #[Route('/mon-compte/nouveau', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(
        Request                   $request,
        UserManager               $userManager,
        MailerManager             $mailer,
        RequestStack              $requestStack
    ): Response
    {
        if (!is_null($this->getUser())) {

            return $this->redirectToRoute('user_edit');
        }
        $user = $userManager->createUser();

        $form = $this->createForm(UserType::class, $user, ['user' => $user]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFileDatas = $form->get('pictures')->getData();

            $userManager->saveOrEditUser($form->getData(), $pictureFileDatas, false);
            // changer vers une route de success de création
            $route = 'front_user_success_creation';
            $param = [];
            $mailer->sendMailNotification(
                $user->getEmail(),
                'emails/create_account_free_login_link.html.twig',
                [
                    'user' => $user,
                ]
            );
            $userManager->saveUser();

            return $this->redirectToRoute($route, $param, Response::HTTP_SEE_OTHER);
        }

        return $this->render('front/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/mon-compte', name: 'user_edit', methods: ['GET', 'POST'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function edit(
        Request     $request,
        UserManager $userManager
    ): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user, ['user' => $user]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFileDatas = $form->get('pictures')->getData();
            $userManager->saveOrEditUser($form->getData(), $pictureFileDatas, true);
            $this->addFlash('success', 'Enregistrement effectué.');

            return $this->redirectToRoute(
                'user_edit',
                [
                ],
                Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * Supprime le compte d'utilisateur connecté
     */
    #[Route('/mon-compte/supprimer', name: 'user_delete', methods: ['POST'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function delete(Request $request, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($this->getUser());
        $entityManager->flush();

        return $this->redirectToRoute('login', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Supprime le compte d'utilisateur connecté
     */
    #[Route('/suppression/{token}', name: 'user_remove_connexion', options: ["expose" => true], methods: ['POST'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function deleteConnexion(
        Request                $request,
        EntityManagerInterface $entityManager,
        string                 $token
    ): Response
    {
        try {
            $user = $this->getUser();
            $user->getMyMatchs()->filter(function (User $userMatch) use ($user, $token) {
                if ($userMatch->getToken() === $token) {
                    $user->removeMyMatch($userMatch);
                }
            });
            $entityManager->flush();
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('chat_show', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Supprime la photo d'un compte utilisateur en base et sur le serveur
     */
    #[Route('/suppression/photo/{token}', name: 'user_remove_picture', methods: ['POST'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function deletePicture(
        string      $token,
        UserManager $userManager
    ): Response
    {
        try {
            $userManager->deleteUserPicture($token, $this->getUser());
            $this->addFlash('success', 'Enregistrement avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('user_edit', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Supprime la photo du compte connecté
     */
    #[Route('/ajout-photo/{token}', name: 'user_add_picture_to_principal', methods: ['POST'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function addPictureToPrincipal(
        string      $token,
        UserManager $userManager
    ): Response
    {
        try {
            $userManager->addUserPictureToPrincipal($token, $this->getUser());
            $this->addFlash('success', 'Enregistrement avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('user_edit', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Affiche le message de succès pour la créatin d'un compte
     */
    #[Route('/creation-compte-valide', name: 'user_success_creation', methods: ['GET'])]
    public function userSuccessCreation(): Response
    {

        return $this->render('user/creation-success.html.twig');
    }

    /**
     * Enregistre les coordonnées GPS d'un utilisateur
     */
    #[Route('/save-coord/{lat}/{lon}', name: 'user_save_coord', options: ["expose" => true], methods: ['POST'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function saveUserCoord(
        string          $lat,
        string          $lon,
        UserManager     $userManager,
        LoggerInterface $logger
    ): Response
    {
        try {
            $user = $this->getUser();
            /**
             * @var User $user
             */
            if ($user) {
                $user->setLat($lat);
                $user->setLon($lon);
                $userManager->saveUser();
            }
        } catch (\Exception $e) {
            // logger l'erreur
            $logger->alert('Erreur sauvegarde GPS user : ' . $e->getMessage());
        }

        return $this->json(['success' => true], Response::HTTP_OK);
    }

    /**
     * Check et récupère via api gouv les coordonnées GPS d'un utilisateur
     */
    #[Route('/check-coord', name: 'user_check_coord', options: ["expose" => true], methods: ['POST'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function checkCoord(UserManager $userManager, LoggerInterface $logger): Response
    {
        try {
            $user = $this->getUser();
            $userManager->checkCoord($user);
        } catch (\Exception $e) {
            // logger l'erreur
            $logger->alert('Erreur sauvegarde GPS user : ' . $e->getMessage());
        }

        return $this->json(['success' => true], Response::HTTP_OK);
    }

    /**
     * @return Response
     */
    #[Route('vu-profil', name: "user_intro_menu", options: ['expose' => true], methods: ['POST'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function seeIntroMenu(UserManager $userManager, LoggerInterface $logger): Response
    {
        try {
            $user = $this->getUser();
            $user->setIntroMenu(true);
            $userManager->saveUser($user);
        } catch (\Exception $e) {
            // logger l'erreur
            $logger->alert('Erreur sauvegarde GPS user : ' . $e->getMessage());
        }

        return $this->json(['success' => true], Response::HTTP_OK);
    }
}
