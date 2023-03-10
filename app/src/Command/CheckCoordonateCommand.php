<?php

namespace App\Command;

use App\Entity\User;
use App\Exception\CriticalSageException;
use App\Repository\UserRepository;
use App\Service\AdresseApi;
use App\Service\CatalogManager;
use App\Service\FileUploadManager;
use App\Service\UserManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Commande de suppression des fichiers uploader via la fonctionnalité
 * DragNDrop
 */
class CheckCoordonateCommand extends Command
{
    protected static $defaultName = 'app:check:coord';

    public function __construct(
        protected UserRepository $userRepository,
        protected UserManager $userManager,
        protected AdresseApi $adresseApi,
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
        $output->writeln("[Spami] Contrôle des coordonnées GPS des compteurs");
        try {
            $users = $this->userRepository->findAll();
            $nbUserTreated = 0;
            foreach ($users as $user) {
                $this->userManager->checkCoord($user);
                $nbUserTreated++;
            }
            $this->logger->info('Nombre de compte contrôlé GPS', ['Nombre de compte contrôlé' => $nbUserTreated]);
            $table = new Table($output);
            $table
                ->setRows([
                    ['Compte', 'contrôlé', $nbUserTreated],
                ]);
            $table->render();
            $output->writeln("[Spami] Fin du contrôle des coordonnées GPS des compteurs");
            return 0;
        } catch (\Exception $e) {
            $this->logger->error("[Spami] Échec du contrôle des coordonnées GPS des compteurs");
            $output->writeln("[Spami] Échec du contrôle des coordonnées GPS des compteurs");
            return 1;
        }
    }
}
