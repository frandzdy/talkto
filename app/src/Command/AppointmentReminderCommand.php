<?php

namespace App\Command;

use App\Entity\TransactionLine;
use App\Service\MailerManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:appointment-reminder',
    description: 'Lance les notifications aux locataire et bailleur pour rappeler la réservation.',
)]
class AppointmentReminderCommand extends Command
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
        $io->comment('Récupération des réservations de demain !');

        try {
            $transactionLineToStarts = $this->em->getRepository(TransactionLine::class)->findBy(
                [
                    'startDate' => (new \DateTime())->modify('+1 days'),
                ]
            );
            $io->comment('nbTransactionToStarts : '.\count($transactionLineToStarts));
            $progess = new ProgressBar($output, \count($transactionLineToStarts));
            $progess->setMessage('Liste des transactions commençant demain');
            $progess->start();
            foreach ($transactionLineToStarts as $transactionLineToStart) {
                /**
                 * @var TransactionLine $transactionLineToStart
                 */
                $lessor = $transactionLineToStart->getProduct()->getAuthor();
                $this->mailerManager->sendMailNotification(
                    $lessor->getEmail(),
                    'emails/reservation_seller_to_start_or_end.html.twig',
                    [
                        'transactionLine' => $transactionLineToStart,
                        'transaction' => $transactionLineToStart->getTransaction(),
                        'type' => 'in',
                    ]
                );
                $renter = $transactionLineToStart->getTransaction()->getAuthor();
                $this->mailerManager->sendMailNotification(
                    $renter->getEmail(),
                    'emails/reservation_renter_to_start_or_end.html.twig',
                    [
                        'transactionLine' => $transactionLineToStart,
                        'transaction' => $transactionLineToStart->getTransaction(),
                        'type' => 'in',
                    ]
                );
                $progess->advance();
            }

            $transactionLineToEnds = $this->em->getRepository(TransactionLine::class)->findBy(
                [
                    'endDate' => new \DateTime(),
                ]
            );
            $io->comment('nbTransactionToEnds : '.\count($transactionLineToEnds));
            $progess = new ProgressBar($output, \count($transactionLineToEnds));
            $progess->setMessage("Liste des transactions finissant aujourd'hui");
            $progess->start();
            foreach ($transactionLineToEnds as $transactionLineToEnd) {
                /**
                 * @var TransactionLine $transactionLineToEnd
                 */
                $lessor = $transactionLineToEnd->getProduct()->getAuthor();
                $this->mailerManager->sendMailNotification(
                    $lessor->getEmail(),
                    'emails/reservation_seller_to_start_or_end.html.twig',
                    [
                        'transactionLine' => $transactionLineToEnd,
                        'transaction' => $transactionLineToEnd->getTransaction(),
                        'type' => 'out',
                    ]
                );
                $renter = $transactionLineToEnd->getTransaction()->getAuthor();
                $this->mailerManager->sendMailNotification(
                    $renter->getEmail(),
                    'emails/reservation_renter_to_start_or_end.html.twig',
                    [
                        'transactionLine' => $transactionLineToEnd,
                        'transaction' => $transactionLineToEnd->getTransaction(),
                        'type' => 'out',
                    ]
                );
                $progess->advance();
            }

            $io->success('Mails send !');

            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $io->success("Erreur lors de l'envoi !");
            $this->emailLogger->error("[Mail] Erreur lors de l'envoie", ['message' => $exception->getMessage()]);

            return Command::FAILURE;
        }
    }
}
