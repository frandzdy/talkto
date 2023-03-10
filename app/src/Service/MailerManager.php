<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;

/**
 * Gestionnaire des envois de mails de l'application
 */
class MailerManager
{
    /**
     * MailerManager constructor.
     */
    public function __construct(private MailerInterface $mailer, private Environment $environment, private LoggerInterface $logger)
    {
    }

    /**
     * Envoi un email
     */
    public function sendMailNotification(
        array | string $to,
        string $templateAlias,
        ?array $vars = array(),
        ?array $pathAttachmentFiles = array(),
        ?string $replyTo = null,
        ?array $bccs = array(),
        ?array $ccs = array()
    ) {
        $template = $this->environment->load($templateAlias);

        $subject = $template->renderBlock('subject', $vars);
        $textBody = $template->renderBlock('body_text', $vars);
        $htmlBody = $template->renderBlock('body_html', $vars);

        $prepend = '';
       
        $email = (new TemplatedEmail());

        if (is_array($to)) {
            foreach ($to as $address) {
                $email->addTo(new Address($address));
            }
        } else {
            $email->to(new Address($to));
        }

        if ($replyTo) {
            $email->replyTo(new Address($replyTo));
        }

        $email->subject($prepend . $subject)
            ->text($textBody)
            ->html($htmlBody);

        foreach ($bccs as $bcc) {
            $email->addBcc(new Address($bcc));
        }
        foreach ($ccs as $cc) {
            $email->addCc(new Address($cc));
        }

        foreach ($pathAttachmentFiles as $pathAttachmentFile) {
            $email->attachFromPath($pathAttachmentFile);
        }

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error(sprintf("Erreur lors de l'envoi du mail : %s", $e->getMessage()));
        }
    }
}
