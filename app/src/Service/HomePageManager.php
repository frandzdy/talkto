<?php

namespace App\Service;

use App\Entity\Picture;
use App\Entity\WebsiteContent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Gestion de la home page.
 */
readonly class HomePageManager
{
    public function __construct(
        private FileUploadManager $fileUploadManager,
        private EntityManagerInterface $em,
    ) {}

    /**
     * Enregistre une home page.
     *
     * @param mixed $homePage
     */
    public function saveHomePage($homePage): void
    {
        $em = $this->em;
        $fileUploadManager = $this->fileUploadManager;
        $homePage->getWebsiteContents()->map(
            static function (WebsiteContent $websiteContent) use ($em, $fileUploadManager): void {
                if ($websiteContent->getUploadedPicture() instanceof UploadedFile) {
                    $filename = $fileUploadManager->uploadFile('home_page', $websiteContent->getUploadedPicture());
                    $picture = (new Picture())->setName($filename);
                    $websiteContent->setPicture($picture);

                    $em->persist($picture);
                }
            }
        );
    }
}
