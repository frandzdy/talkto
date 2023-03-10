<?php

namespace App\Command;

use App\Repository\UserRepository;
use App\Service\MatchManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Commande de suppression des fichiers uploader via la fonctionnalité
 * DragNDrop
 */
class GenerateMatchsCommand extends Command
{
    protected static $defaultName = 'app:generate:matchs';

    public function __construct(
        protected UserRepository $userRepository,
        protected MatchManager $matchManager,
        protected LoggerInterface $logger,
        string $name = null
    )
    {
        parent::__construct($name);
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setDescription('Réinitialise le compte de temps et le nombre de spam qu\'un utilisateur peut avoir')
            ->setHelp('Cette commande permet de réinitialise le compte de temps et le nombre de spam qu\'un utilisateur peut avoir')
        ;
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $output->writeln("[Spami] Génère les matchs pour chaque utilisateur");
        try {
            $users = $this->userRepository->findAll();
            $nbUserTreated = 0;
            foreach ($users as $user) {
                $this->matchManager->getMatchs($user);
                $nbUserTreated++;
            }
            $this->logger->info('Nombre de compte traités', ['Nombre de fiche traité' => $nbUserTreated]);
            $table = new Table($output);
            $table
                ->setRows([
                    ['Compte', 'traité', $nbUserTreated],
                ]);
            $table->render();
            $output->writeln("[Spami] Fin de la génération les matchs pour chaque utilisateur");
            return 0;
        } catch (\Exception $e) {
            $this->logger->error("[Spami] Échec de la génération les matchs pour chaque utilisateur");
            $output->writeln("[Spami] Échec de la génération les matchs pour chaque utilisateur");
            return 1;
        }
    }
}
