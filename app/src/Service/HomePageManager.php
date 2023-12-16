<?php

namespace App\Service;

use App\Entity\Picture;
use App\Entity\WebsiteContent;
use App\Model\ContactModel;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

/**
 * Gestion de la home page
 */
readonly class HomePageManager
{
    public function __construct(
        private FileUploadManager $fileUploadManager,
        private EntityManagerInterface $em,
    ) {
    }

    /**
     * Enregistre une home page
     */
    public function saveHomePage($homePage): void
    {
        $em = $this->em;
        $fileUploadManager = $this->fileUploadManager;
        $homePage->getWebsiteContents()->map(
            function (WebsiteContent $websiteContent) use ($em, $homePage, $fileUploadManager) {
                if ($websiteContent->getUploadedPicture()) {
                    $filename = $fileUploadManager->uploadFile('home_page', $websiteContent->getUploadedPicture());
                    $picture = (new Picture())->setName($filename);
                    $websiteContent->setPicture($picture);

                    $em->persist($picture);
                }
            }
        );
    }
}
