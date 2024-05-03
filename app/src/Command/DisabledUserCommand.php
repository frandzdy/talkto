<?php

namespace App\Command;

use App\Entity\Product;
use App\Entity\User;
use App\Enum\ProductStatus;
use App\Service\MailerManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Clock\DatePoint;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:disable-user',
    description: 'Lance les notifications aux locataire et bailleur pour signaler la fermeture de celui-ci.',
)]
class DisabledUserCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly MailerManager $mailerManager,
        private readonly LoggerInterface $emailLogger
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->comment('Récupération des clients !'.(new DatePoint('-11 months'))->format('d-m-Y'));

        try {
            $users = $this->em->getRepository(User::class)->getUserInactive(
                new DatePoint('-11 months')
            );
            $io->comment('nbUser 30 before end : '.\count($users));
            $progess = new ProgressBar($output, \count($users));
            $progess->setMessage('Liste des comptes à 11 mois d\'inactivité');
            $progess->start();
            foreach ($users as $user) {
                // @var User $user
                $this->mailerManager->sendMailNotification(
                    $user->getEmail(),
                    'emails/warning_user_account_close.html.twig',
                    [
                        'user' => $user,
                    ]
                );
                $progess->advance();
            }

            $userToCloses = $this->em->getRepository(User::class)->getUserInactive(
                new DatePoint('-12 months')
            );
            $io->comment('nbUserToEnds : '.\count($userToCloses));
            $progess = new ProgressBar($output, \count($userToCloses));
            $progess->setMessage('Liste des comptes à clôturer !');
            $progess->start();
            foreach ($userToCloses as $userToClose) {
                if (User::ROLE_SELLER === $userToClose->getRole()) {
                    // on doit désactiver tous les produits
                    $product = $this->em->getRepository(Product::class)->findBy(['author' => $userToClose->getId()]);
                    if ($product) {
                        $product->setStatus(ProductStatus::REJECTED);
                    }
                }

                $userToClose->setDeletedAt(new \DateTime());
                // @var User $userToClose
                $this->mailerManager->sendMailNotification(
                    $userToClose->getEmail(),
                    'emails/user_account_close.html.twig',
                    [
                        'user' => $userToClose,
                    ]
                );
                $progess->advance();
            }

            $io->success('Mails send !');

            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $io->success("Erreur lors de l'envoi !");
            $this->emailLogger->error("[Mail] Erreur lors de l'envoi", ['message' => $exception->getMessage()]);

            return Command::FAILURE;
        }
    }
}
