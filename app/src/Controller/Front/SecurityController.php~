<?php
    
    namespace App\Controller;
    
    use App\Form\LoginType;
    use App\Repository\UserRepository;
    use App\Service\MailerManager;
    use App\Service\UserManager;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Notifier\NotifierInterface;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
    use Symfony\UX\Turbo\TurboBundle;

    class SecurityController extends AbstractController
    {
        #[Route('/login', name: 'login', methods: ['POST', 'GET'])]
        public function login(
            NotifierInterface $notifier,
            LoginLinkHandlerInterface $loginLinkHandler,
            MailerManager $mailer,
            UserRepository $userRepository,
            Request $request,
            UserManager $userManager
        ): Response {
            
            if ($this->getUser()) {
                return $this->redirectToRoute('user_edit');
            }
    
            $data = null;
            $form = $this->createForm(
                LoginType::class,
                $data,
                [
                    'action' => $this->generateUrl('login'),
                    'method' => 'POST'
                ]
            );
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $user = $userRepository->findOneBy(['email' => $data['email']]);
                if ($user) {
                    $loginLinkDetails = $loginLinkHandler->createLoginLink($user);
                    echo '<pre>';
                    var_dump($loginLinkDetails->getUrl());
                    echo '</pre>';
                    echo 'Répertoire : ' . __DIR__ . ' Ligne : ' . __LINE__ . ' Méthode : ' . __METHOD__ . ' Debug Frandzdy';
                    die;
                    $mailer->sendMailNotification(
                        $user->getEmail(),
                        'emails/login_link.html.twig',
                        [
                            'link' => $loginLinkDetails->getUrl(),
                            'user' => $user,
                            'expiresAt' => $loginLinkDetails->getExpiresAt()
                        ]
                    );
                    // 🔥 The magic happens here! 🔥
                    if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                        // If the request comes from Turbo, only send the HTML to update using a TurboStreamResponse
                        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
                        return $this->render('security/login_link_sent.stream.html.twig');
                    }
                    $this->addFlash('Un lien de connexion vient de vous êtes envoyer.');
                    // render a "Login link is sent!" page si pas turbo activer
                    return $this->redirectToRoute('login', [], Response::HTTP_SEE_OTHER);
                } else {
                    // 🔥 The magic happens here! 🔥
                    if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                        // If the request comes from Turbo, only send the HTML to update using a TurboStreamResponse
                        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
                        return $this->render('security/login_error.stream.html.twig');
                    }
                }
            }
            
            return $this->renderForm(
                'security/login.html.twig',
                [
                    'form' => $form
                ]
            );
        }
        
        #[Route('/login_check', name: 'login_check')]
        public function check(Request $request)
        {
            // get the login link query parameters
            $expires = $request->query->get('expires');
            $username = $request->query->get('user');
            $hash = $request->query->get('hash');
            
            // and render a template with the button
            return $this->render('security/process_login_link.html.twig', [
                'expires' => $expires,
                'user' => $username,
                'hash' => $hash,
            ]);
        }
        
        #[Route("/logout", name: "logout")]
        public function logout(): void
        {
            throw new \LogicException(
                'This method can be blank - it will be intercepted by the logout key on your firewall.'
            );
        }
    }
