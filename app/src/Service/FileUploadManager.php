<?php

namespace App\Service;

use App\Exception\FileNotFoundException;
use Liip\ImagineBundle\Message\WarmupCache;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Gestionnaire des fichiers de l'application.
 */
class FileUploadManager
{
    /**
     * Constructor.
     *
     * @param mixed $fileUploadParameters
     */
    public function __construct(
        protected LoggerInterface $fileLogger,
        protected $fileUploadParameters,
        protected SluggerInterface $slugger,
        protected string $env,
        protected MessageBusInterface $messageBus
    ) {}

    /**
     * Retourne vrai si le dossier est bien authorisé.
     */
    public function isDirectoryValid(string $directory): bool
    {
        return isset($this->fileUploadParameters['directories'][$directory]);
    }

    /**
     * Retourne vrai si le fichier demandé à un nom valide.
     */
    public function isFilePatternAuthorized(string $fileName): bool
    {
        return preg_match('/^[a-z0-9A-Z]+\.[a-zA-Z0-9]+$/', $fileName);
    }

    /**
     * Déplace un fichier dans le dossier qui lui correspond et retourne son nouveau nom.
     */
    public function uploadFile(string $directory, UploadedFile $file): string
    {
        try {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $fileName = $safeFilename.'-'.\uniqid().'.'.$file->guessExtension();
            $file->move($this->getDirectoryPath($directory), $fileName);

            // Utilisation du service WarmupCache pour redimensionner les images
            $this->messageBus->dispatch(new WarmupCache($this->getDirectoryPath($directory).$fileName));

            return $fileName;
        } catch (\Exception $exception) {
            $this->fileLogger->error('Erreur upload fichier : ', ['message' => $exception->getMessage()]);

            return '';
        }
    }

    /**
     * Déplace un fichier dans le dossier qui lui correspond et retourne son nouveau nom.
     */
    public function uploadPrivateFile(string $directory, UploadedFile $file): string
    {
        try {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $fileName = $safeFilename.'-'.\uniqid().'.'.$file->guessExtension();
            $file->move($this->getDirectoryPrivatePath($directory), $fileName);

            return $fileName;
        } catch (\Exception $exception) {
            $this->fileLogger->error('Erreur upload fichier : ', ['message' => $exception->getMessage()]);

            return '';
        }
    }

    /**
     * Retourne le contenu d'un fichier.
     */
    public function getFileContent(string $directory, string $filename): string
    {
        $path = $this->getDirectoryPath($directory).$filename;

        if (file_exists($path)) {
            return file_get_contents($path);
        }

        throw new FileNotFoundException();
    }

    /**
     * Supprime un fichier du répertoire.
     */
    public function removeFile(string $directory, string $filename): bool
    {
        $path = $this->getDirectoryPath($directory).$filename;
        if (!is_file($path) || !is_writable($path)) {
            return false;
        }

        return unlink($path);
    }

    /**
     * Supprime un fichier du répertoire liip.
     */
    public function removeFileLiip(string $directory, string $filename): bool
    {
        $path = $this->getDirectoryPathLiip().'/'.$directory.'/uploads/profile_picture/'.$filename;
        if (!is_file($path) || !is_writable($path)) {
            return false;
        }

        return unlink($path);
    }

    /**
     * Supprime tous les fichiers d'un répertoire.
     */
    public function removeAllFiles(string $directory): int
    {
        $nbFileRemove = 0;
        // on recupère le repertoire
        $path = $this->getDirectoryPath($directory);
        // si c'est un dossier
        // on l'ouvre
        if (is_dir($path) && ($dh = opendir($path))) {
            // tant que l'on peut lire le répertoire
            while (($element = readdir($dh)) !== false) {
                // si le fichier n'est un pas un dossier
                if ('.' != $element && '..' != $element && !is_dir($path.'/'.$element)) {
                    // on le supprime
                    $this->removeFile($directory, $element);
                    ++$nbFileRemove;
                }
            }
        }

        return $nbFileRemove;
    }

    /**
     * Retourne le nom du répertoire recherché.
     */
    private function getDirectoryPath(string $directory): string
    {
        $path = $this->fileUploadParameters['base_path'];

        return $path.($this->fileUploadParameters['directories'][$directory] ?? $this->fileUploadParameters['directories']['default']);
    }

    /**
     * Retourne le nom du répertoire Liip pour la précache.
     */
    private function getDirectoryPathLiipWarmup(string $directory): string
    {
        $path = $this->fileUploadParameters['base_path_twig_warmup'];

        return $path.($this->fileUploadParameters['directories'][$directory] ?? $this->fileUploadParameters['directories']['default']);
    }

    /**
     * Retourne le nom du répertoire recherché.
     */
    private function getDirectoryPrivatePath(string $directory): string
    {
        $path = $this->fileUploadParameters['base_path_private'];

        return $path.($this->fileUploadParameters['directories'][$directory] ?? $this->fileUploadParameters['directories']['default']);
    }

    /**
     * Retourne le nom du répertoire recherché.
     */
    private function getDirectoryPathLiip(): string
    {
        return $this->fileUploadParameters['base_path_liip'];
    }
}
