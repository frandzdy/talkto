<?php

namespace App\Controller\Front;

use App\Exception\FileNotFoundException;
use App\Service\FileUploadManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controlleur de récupération des fichiers stockés sur le serveur
 */
#[Route(path: '/fichiers', name: 'file_')]
class FileController extends AbstractController
{
    public const MIMES = [
        'qualification' => 'image/svg+xml',
        'homepage' => 'image/png',
        'faqs' => 'image/svg+xml',
        'testimonial' => 'image/jpeg'
    ];

    /**
     * Récupère un logo stocké sur le serveur
     */
    #[Route(path: '/{directory}/{fileName}', name: 'get', methods: ['GET'])]
    public function getFile(string $directory, string $fileName, FileUploadManager $filerUploader): Response
    {
        if (
            !$filerUploader->isDirectoryValid($directory)
            || !$filerUploader->isFilePatternAuthorized($fileName)
        ) {
            throw $this->createAccessDeniedException();
        }

        try {
            $options = [];
            if (isset(self::MIMES[$directory])) {
                $options['content-type'] = self::MIMES[$directory];
            }

            $response = new Response(
                $filerUploader->getFileContent($directory, $fileName),
                Response::HTTP_OK,
                $options
            );

            $response->setExpires(new \DateTime('+1 year'));

            return $response;
        } catch (FileNotFoundException) {
            throw $this->createNotFoundException();
        }
    }
}
