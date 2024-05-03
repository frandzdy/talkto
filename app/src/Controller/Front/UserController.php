<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Form\Front\UserType;
use App\Repository\UserRepository;
use App\Security\FrontAuthenticator;
use App\Service\MailerManager;
use App\Service\StripeManager;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    #[Route('/mon-compte', name: 'user_account', options: ['expose' => true], methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function account(
        UserRepository $userRepository,
        SessionInterface $session,
        StripeManager $stripeManager
    ): Response {
        $user = $this->getUser();
        $carts = $session->get('cart', [
            'products' => [],
            'totalQuantity' => 0,
            'totalAmount' => 0,
            'totalTva' => 0,
            'totalFees' => 0,
            'transactionId' => null,
        ]);
        $collections = [
            'reservations' => $userRepository->getReservations($user, 0),
            'products' => $userRepository->getProducts($user, 0),
        ];

        if (User::ROLE_SELLER === $user->getRole()) {
            $collections['rents'] = $userRepository->getRents($user, 0);
        }

        // @var User $user
        $collections['wishlists'] = $userRepository->getWishlists($user, 0);

        $urlActivationAccount = '';
        $urlActivation = false;
        if (!$user->getIsStripeAccountActive() && $user->getStripeAccountId()) {
            $urlActivationAccount = $stripeManager->createAccountLink($user)->url;
            $urlActivation = true;
        }

        return $this->render(
            'front/user/byer/account.html.twig',
            ['user' => $user, 'collections' => $collections, 'carts' => $carts, 'urlActivationAccount' => $urlActivationAccount, 'urlActivation' => $urlActivation]
        );
    }

    #[Route('/creation-compte', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        UserManager $userManager,
        MailerManager $mailer,
        Security $security
    ): Response {
        if ($this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('front_user_account');
        }

        $user = $userManager->createUser();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFileData = $form->get('uploadPicture')->getData();

            $userManager->saveOrEditUser($form->getData(), $pictureFileData);
            // changer vers une route de success de création
            $param = [];
            $mailer->sendMailNotification(
                $user->getEmail(),
                'emails/create_account.html.twig',
                [
                    'user' => $user,
                ]
            );
            $userManager->saveUser();

            // substitute the previous line (redirect response) with this one.
            return $security->login($user, FrontAuthenticator::class, 'front');
        }

        return $this->render('front/user/byer/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/edition-compte', name: 'user_edit', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function edit(
        Request $request,
        UserManager $userManager
    ): Response {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user, ['action' => $request->getRequestUri(), 'edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFileData = $form->get('uploadPicture')->getData();

            $userManager->saveOrEditUser($form->getData(), $pictureFileData);
            $this->addFlash('success', 'Modification effectué.');

            return $this->json(
                [
                    'success' => true,
                    'redirectUrl' => $this->generateUrl('front_user_account'),
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
     * Désactiver le compte d'utilisateur connecté.
     */
    #[Route('/mon-compte/desactivation', name: 'user_deactivate', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function deactivate(EntityManagerInterface $em, Security $security, MailerManager $mailerManager): Response
    {
        $user = $this->getUser();
        $user->setDeletedAt(new \DateTime());

        $em->flush();

        $mailerManager->sendMailNotification(
            $user->getEmail(),
            'emails/account_deactivate.html.twig',
            [
                'user' => $user,
            ]
        );

        $security->logout(false);

        $this->addFlash('success', 'Compte désactiver');

        return $this->redirectToRoute('front_home');
    }

    /**
     * Activer le compte d'utilisateur connecté.
     */
    #[Route('/mon-compte/activer/{token}', name: 'user_activate', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function activate(string $token, EntityManagerInterface $em, Security $security): Response
    {
        $user = $em->getRepository(User::class)->findBy(
            [
                'token' => $token,
            ]
        );
        $user->setDeletedAt(null);

        $security->login($user, FrontAuthenticator::class, 'front');

        $this->addFlash('success', 'Compte désactiver');

        return $this->redirectToRoute('front_home');
    }

    /**
     * Supprime la photo d'un compte utilisateur en base et sur le serveur.
     */
    #[Route('/suppression/photo/{token}', name: 'user_remove_picture', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function deletePicture(
        string $token,
        UserManager $userManager
    ): Response {
        try {
            $userManager->deleteUserPicture($token, $this->getUser());
            $this->addFlash('success', 'Suppression effectué.');
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
        }

        return $this->redirectToRoute('user_edit', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Affiche le message de succès pour la créatin d'un compte.
     */
    #[Route('/creation-compte-valide', name: 'user_success_creation', methods: ['GET'])]
    public function userSuccessCreation(): Response
    {
        return $this->render('front/user/partials/creation-success.html.twig');
    }

    /**
     * Pagination des blocs table de la fiche qualif.
     */
    #[Route(path: '/collections/{name}/{page}', name: 'user_collection', requirements: ['name' => 'reservations|rents|products|wishlists'], methods: ['GET'])]
    public function collection(string $name, int $page, EntityManagerInterface $em): Response
    {
        $func = 'get'.ucwords($name);

        return $this->render(
            'front/user/partials/_'.$name.'.html.twig',
            [
                'results' => $em->getRepository(User::class)->{$func}($this->getUser(), $page - 1),
            ]
        );
    }

    /**
     * Enregistre les coordonnées GPS d'un utilisateur.
     */
    #[Route('/save-coord/{lat}/{lon}', name: 'user_save_coord', options: ['expose' => true], methods: ['POST'])]
    public function saveUserCoord(
        string $lat,
        string $lon,
        UserManager $userManager,
        LoggerInterface $logger,
        SessionInterface $session
    ): Response {
        try {
            $user = $this->getUser();
            // @var User $user
            if ($user instanceof UserInterface) {
                $user->setLat($lat);
                $user->setLon($lon);
                $userManager->saveUser();
            } else {
                $session->set('lat', $lat);
                $session->set('lon', $lon);
            }
        } catch (\Exception $exception) {
            // logger l'erreur
            $logger->alert('Erreur sauvegarde GPS user : '.$exception->getMessage());
        }

        return $this->json(['success' => true], Response::HTTP_OK);
    }
}
