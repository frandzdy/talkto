<?php

namespace App\Service;

use App\Enum\Civility;
use App\Entity\Picture;
use App\Entity\Product;
use App\Entity\Reservation;
use App\Entity\TransactionLine;
use App\Entity\User;
use App\Enum\ProductStatus;
use App\Repository\PictureRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ProductManager
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly FileUploadManager      $fileUploadManager,
        private readonly LoggerInterface $logger,
        private readonly ReservationRepository $reservationRepository
    ) {}

    /**
     * Retourne un user prêt pour la création soit byer soit seller
     */
    public function createProduct(User $user): Product
    {
        return (new Product())
            ->setAuthor($user)
            ->setStatus(ProductStatus::WAITING)
            ->setQuantityAllReadyReserved(0)
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
                    $this->em->persist($pic);
                    $product->addPicture($pic);
                }
            }
        }

        $product->setStatus(ProductStatus::WAITING);

        if (!$update) {
            $this->em->persist($product);
        }

        $this->em->flush();
        
        return true;
    }

    /**
     * Supprime une photo du compte utilisateur serveur et bdd
     */
    public function deleteProductPicture(Product $product, Picture $pictureToRemove): bool
    {
        try {
            // on supprime le fichier du serveur du compte
            foreach ($product->getPictures() as $picture) {
                if ($picture === $pictureToRemove && $product->getPictures()->contains($pictureToRemove)) {
                    $product->removePicture($picture);
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
                    $this->em->remove($picture);
                }
            }

            $this->em->flush();

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
        $this->em->flush();
    }
    /**
     * Supprime un produit
     */
    public function deleteProduct(Product $product): void
    {
        /**
         *  Contrôle que le produit n'est plus en location et pas réserver.
         */
        if (!$this->em->getRepository(TransactionLine::class)->productHaveTransactionInProgress($product)) {
            $this->em->remove($product);
            $this->saveProduct();
        }
    }

    /**
     * @param array $cart
     * @param mixed $flatpickrDate
     * @param Product|null $product
     * @param mixed $quantity
     * @return array
     */
    public function addProductToCart(array $cart, mixed $flatpickrDate, ?Product $product, mixed $quantity): array
    {
        $totalQuantity = 0;
        $totalAmount = 0;

        if (str_contains($flatpickrDate, 'au')) {
            $startDate = new \DateTimeImmutable(trim(explode('au', $flatpickrDate)[0]));
            $endDate = new \DateTimeImmutable(trim(explode('au', $flatpickrDate)[1]));

        } else {
            $startDate = new \DateTimeImmutable($flatpickrDate);
            $endDate = $startDate;
        }
        $numberDays = $startDate->diff($endDate)->days === 0 ? 1 : $startDate->diff($endDate)->days;
        $pictureName = '';
        if ($product->getPictures()->count() > 0) {
            $pictureName = $product->getPictures()->first()->getName();
        }
        $cart['products'][$product->getToken()] = [
            'caution' => $product->getCaution(),
            'price' => $product->getAmount(),
            'quantity' => $quantity,
            'flatpickrDate' => $flatpickrDate,
            'startDate' => $startDate->format('d/m/Y'),
            'endDate' => $endDate->format('d/m/Y'),
            'numberDays' => $numberDays,
            'pictureName' => $pictureName,
            'title' => $product->getTitle(),
            'disabledDates' => json_encode($this->getDisabledDatesFormProduct($product->getToken()))
        ];
        foreach ($cart['products'] as $item) {
            $totalQuantity += (int)$item['quantity'];
            $totalAmount += (int)$item['price'] * (int)$item['quantity'] * (int)$item['numberDays'];
        }
        $cart['totalQuantity'] = $totalQuantity;
        $cart['totalAmount'] = $totalAmount + ($totalAmount * 0.1);
        $cart['totalTva'] = $totalAmount * 0.2;
        $cart['totalFees'] = $totalAmount * 0.1;

        return $cart;
    }

    /**
     * @param string $token
     * @return array
     */
    public function getDisabledDatesFormProduct(string $token): array
    {
        $disabledDates = [];
        if ($reservations = $this->reservationRepository->getAvailableProducts($token)) {
            foreach ($reservations as $reservation) {
                /**
                 * @var Reservation $reservation
                 */
                $transaction = $reservation->getTransaction();
                foreach ($transaction->getTransactionLines() as $transactionLine) {
                    /**
                     * @var TransactionLine $transactionLine
                     */
                    $disabledDates[] = [
                        'from' => ($transactionLine->getStartDate())->format('Y-m-d'),
                        'to' => ($transactionLine->getEndDate())->format('Y-m-d'),
                    ];
                }
            }
        }

        return $disabledDates;
    }
}
