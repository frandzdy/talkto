<?php

namespace App\Controller\Front;

use App\Form\Front\ContactType;
use App\Model\ContactModel;
use App\Service\ContactManager;
use App\Service\MailerManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Cache(maxage: '3600')]
class ContactController extends AbstractController
{
    #[Route('/contactez-nous', name: 'contact', methods: ['GET', 'POST'])]
    public function contactUs(
        Request $request,
        ContactManager $contactManager,
        MailerManager $mailerManager,
        string $emailSupport
    ): Response {
        if ($this->getUser() instanceof UserInterface) {
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
                'contact' => $contact,
            ];
            $mailerManager->sendMailNotification(
                $emailSupport,
                'emails/contact.html.twig',
                $vars,
                from: $contact->getEmail()
            );
            $this->addFlash('success', 'Votre message a été envoyé.');

            return $this->redirectToRoute('front_contact');
        }

        return $this->render('front/contact/index.html.twig', ['form' => $form]);
    }
}
