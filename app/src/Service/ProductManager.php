<?php

namespace App\Service;

use App\Entity\Genre;
use App\Entity\Picture;
use App\Entity\Product;
use App\Entity\User;
use App\Enum\ProductStatus;
use App\Repository\PictureRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FileUploadManager      $fileUploadManager,
        private readonly LoggerInterface $logger
    )
    {
    }

    /**
     * Retourne un user prêt pour la création soit byer soit seller
     */
    public function createProduct(User $user): Product
    {
        return (new Product())
            ->setToken(hash('sha256', random_bytes(32)))
            ->setUser($user)
            ->setStatus(ProductStatus::WAITING)
        ;
    }

    /**
     * Créer ou met à jour un produit
     */
    public function saveOrEditProduct(Product $product, array $pictureFileDatas, bool $update = false): bool
    {
        if ($pictureFileDatas) {
            foreach ($pictureFileDatas as $pictureFileData) {
                if ($pictureFileData instanceof UploadedFile) {
                    $fileName = $this->fileUploadManager->uploadFile('product_picture', $pictureFileData);
                    $pic = new Picture();
                    $pic->setName($fileName);
                    $pic->setToken(hash('sha256', random_bytes(32)));
                    $this->entityManager->persist($pic);
                    $product->addPicture($pic);
                }
            }
        }

        if (!$update) {
            $this->entityManager->persist($product);
        }

        $this->entityManager->flush();
        
        return true;
    }

    /**
     * Supprime une photo du compte utilisateur serveur et bdd
     */
    public function deleteProductPicture(Product $product): bool
    {
        try {
            // on supprime le fichier du serveur du compte
            foreach ($product->getPictures() as $picture) {
                $product->removePicture($picture);
                $this->fileUploadManager->removeFile('product_picture', $picture->getName());
                $this->entityManager->remove($picture);
            }

            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            $this->logger->alert("Erreur lors de la suppression de la photo du produit : " . $e->getMessage());

            return false;
        }
    }

    /**
     * flush en base de données la création ou la mise à jour
     */
    public function saveProduct(): void
    {
        $this->entityManager->flush();
    }
    /**
     * Supprime un produit
     */
    public function deleteProduct(Product $product): void
    {
        /**
         *  Contrôle que le produit n'est plus en location et pas réserver.
         */
        $this->entityManager->remove($product);
        $this->saveProduct();
    }
}
