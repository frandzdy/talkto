<?php

namespace App\Controller\Front;

use App\Entity\Product;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\ProductRepository;
use App\Repository\ReservationRepository;
use App\Security\FrontAuthenticator;
use App\Service\MailerManager;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class UserController extends AbstractController
{
    #[Route('/mon-compte', name: 'user_account', options: ['expose' => true], methods: ['GET'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function account(EntityManagerInterface $em, SessionInterface $session): Response
    {
        $user = $this->getUser();
        $carts = $session->get('cart', [
            'products' => [],
            'totalQuantity' => 0,
            'totalAmount' => 0,
            'totalTva' => 0,
            'paymentIntentId' => null
        ]);
        $collections = [
            'reservations' => $em->getRepository(User::class)->getReservations($user, 0),
            'products' => $em->getRepository(User::class)->getProducts($user, 0),
            'sellers' => $em->getRepository(User::class)->getSellers($user, 0),
        ];

        return $this->render('front/user/byer/account.html.twig', compact('user', 'collections', 'carts'));
    }

    #[Route('/creation-compte', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        UserManager $userManager,
        MailerManager $mailer,
        Security $security,
        FrontAuthenticator $frontAuthenticator
    ): Response
    {
        if (!is_null($this->getUser())) {

            return $this->redirectToRoute('front_user_edit');
        }
        $user = $userManager->createUser();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFileData = $form->get('picture')->getData();

            $userManager->saveOrEditUser($form->getData(), $pictureFileData);
            // changer vers une route de success de création
            $param = [];
            $mailer->sendMailNotification(
                $user->getEmail(),
                'front/emails/create_account.html.twig',
                [
                    'user' => $user,
                ]
            );
            $userManager->saveUser();

            // substitute the previous line (redirect response) with this one.
            return $security->login($user, 'frontAuthenticator', 'front');
        }

        return $this->render('front/user/byer/edit.html.twig', [
            'user' => $user,
            'form' => $form
        ]);
    }

    #[Route('/edition-compte', name: 'user_edit', methods: ['GET', 'POST'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function edit(
        Request     $request,
        UserManager $userManager
    ): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user, ['action' => $request->getRequestUri()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFileData = $form->get('picture')->getData();

            $userManager->saveOrEditUser($form->getData(), $pictureFileData);
            $this->addFlash('success', 'Enregistrement effectué.');

            return $this->json(
                [
                    'success' => true,
                    'redirectUrl' => $this->generateUrl('front_user_edit')
                ],
                Response::HTTP_OK
            );
        }

        return $this->render('front/user/partials/_form.html.twig', [
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

        return $this->render('front/user/partials/creation-success.html.twig');
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
     * Pagination des blocs table de la fiche qualif
     */
    #[Route(path: '/collections/{name}/{page}', name: "user_collection", requirements: ['name' => 'reservations|products|sellers'], methods: ["GET"])]
    public function collection(string $name, int $page, EntityManagerInterface $em): Response
    {
        $func = "get" . ucwords($name);

        return $this->render(
            'front/user/partials/_' . $name . '.html.twig',
            [
                'results' => $em->getRepository(User::class)->$func($this->getUser(), $page - 1)
            ]
        );
    }
}
