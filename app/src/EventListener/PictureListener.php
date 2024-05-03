<?php

namespace App\EventListener;

use App\Entity\Picture;
use App\Service\FileUploadManager;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreRemoveEventArgs;

#[AsEntityListener(event: PreRemoveEventArgs::class, method: 'preRemove', entity: Picture::class)]
readonly class PictureListener
{
    public function __construct(private FileUploadManager $fileUploadManager) {}

    /**
     * Supprime les images sur le disque avant la suppression.
     */
    public function preRemove(Picture $picture, PreRemoveEventArgs $event): void
    {
        if ('' !== $this->fileUploadManager->getFileContent('product', $picture->getName()) && '0' !== $this->fileUploadManager->getFileContent('product', $picture->getName())) {
            $this->fileUploadManager->removeFile('product_picture', $picture->getName());
            $this->fileUploadManager->removeFileLiip('product_modal', $picture->getName());
            $this->fileUploadManager->removeFileLiip('product_modal_miniature', $picture->getName());
            $this->fileUploadManager->removeFileLiip('product_trends_or_sale', $picture->getName());
            $this->fileUploadManager->removeFileLiip('product_details', $picture->getName());
            $this->fileUploadManager->removeFileLiip('product_miniature_details', $picture->getName());
            $this->fileUploadManager->removeFileLiip('product_miniature', $picture->getName());
            $this->fileUploadManager->removeFileLiip('home_slider', $picture->getName());
            $this->fileUploadManager->removeFileLiip('home_under_slider', $picture->getName());
            $this->fileUploadManager->removeFileLiip('home_mid', $picture->getName());
            $this->fileUploadManager->removeFileLiip('home_latest', $picture->getName());
        } else {
            $this->fileUploadManager->removeFile('profile_picture', $picture->getName());
            $this->fileUploadManager->removeFileLiip('profil', $picture->getName());
            $this->fileUploadManager->removeFileLiip('profil_miniature', $picture->getName());
        }
    }
}
