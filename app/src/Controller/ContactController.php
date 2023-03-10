<?php
    
    namespace App\Controller;
    
    use App\Form\ContactType;
    use App\Model\ContactModel;
    use App\Service\ContactManager;
    use App\Service\MailerManager;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\UX\Turbo\Stream\TurboStreamResponse;

    class ContactController extends AbstractController
    {
        #[Route('/contactez-nous', name: 'app_contact', methods: ['GET', 'POST'])]
        public function contactUs(
            Request $request,
            ContactManager $contactManager,
            MailerManager $mailerManager,
            string $emailContact
        ): Response|string {
            if ($this->getUser()) {
                $contact = $contactManager->initializeContact(
                    $this->getUser()->getEmail(),
                    $this->getUser()->getLastname(),
                    $this->getUser()->getFirstname()
                );
            } else {
                $contact = new ContactModel();
            }
            $form = $this->createForm(ContactType::class, $contact);
            if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
                $vars = [
                    'contact' => $contact
                ];
                $mailerManager->sendMailNotification(
                    // params
                    $emailContact,
                    'emails/contact.html.twig',
                    $vars
                );
                $this->addFlash('success', 'Votre message a été envoyé.');
                
                return $this->redirectToRoute('app_contact');
            }
            
            return $this->renderForm('contact/index.html.twig', ['form' => $form]);
        }
    }
