<?php

namespace App\Command;

use App\Repository\UserRepository;
use App\Service\UserManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Commande de suppression des fichiers uploader via la fonctionnalité
 * DragNDrop
 */
class ReactiveAccountCommand extends Command
{
    protected static $defaultName = 'app:reactivate:account';

    public function __construct(
        protected UserRepository $userRepository,
        protected UserManager $userManager,
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
        $output->writeln("[Spami] Réactivation des compteurs");
        try {
            $nbUserTreated = $this->userManager->reactivateAccount();
            $this->logger->info('Nombre de réactivation de compte', ['Nombre de fiche réactivé' => $nbUserTreated]);
            $table = new Table($output);
            $table
                ->setRows([
                    ['Compte', 'réactivé', $nbUserTreated],
                ]);
            $table->render();
            $output->writeln("[Spami] Fin Réactivation des compteurs");
            return 0;
        } catch (\Exception $e) {
            $this->logger->error("[Spami] Échec Réactivation des compteurs");
            $output->writeln("[Spami] Échec Réactivation des compteurs");
            return 1;
        }
    }
}
