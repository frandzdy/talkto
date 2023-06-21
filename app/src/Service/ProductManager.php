<?php

namespace App\Service;

use App\Entity\Genre;
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
        private readonly EntityManagerInterface $entityManager,
        private readonly FileUploadManager      $fileUploadManager,
        private readonly LoggerInterface $logger,
        private readonly ReservationRepository $reservationRepository
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
            ->setAuthor($user)
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

    /**
     * @param SessionInterface $session
     * @param mixed $flatpickrDate
     * @param Product|null $product
     * @param mixed $quantity
     * @return array
     */
    public function addProductToCart(SessionInterface $session, mixed $flatpickrDate, ?Product $product, mixed $quantity): array
    {
        $totalQuantity = 0;
        $totalAmount = 0;
        $cart = $session->get('cart', [
            'products' => [],
            'totalQuantity' => 0,
            'totalAmount' => 0,
            'totalTva' => 0,
            'totalFees' => 0,
            'paymentIntentId' => null,
            'transactionId' => null
        ]);

        if (str_contains($flatpickrDate, 'au')) {
            $startDate = new \DateTimeImmutable(trim(explode('au', $flatpickrDate)[0]));
            $endDate = new \DateTimeImmutable(trim(explode('au', $flatpickrDate)[1]));

        } else {
            $startDate = new \DateTimeImmutable($flatpickrDate);
            $endDate = $startDate;
        }
        $numberDays = $startDate->diff($endDate)->days === 0 ? 1 : $startDate->diff($endDate)->days;
        $cart['products'][$product->getToken()] = [
            'caution' => $product->getCaution(),
            'price' => $product->getAmount(),
            'quantity' => $quantity,
            'flatpickrDate' => $flatpickrDate,
            'startDate' => $startDate->format('d/m/Y'),
            'endDate' => $endDate->format('d/m/Y'),
            'numberDays' => $numberDays,
            'pictureName' => $product->getPictures()->first()->getName(),
            'title' => $product->getTitle()
        ];
        foreach ($cart['products'] as $item) {
            $totalQuantity += (int)$item['quantity'];
            $totalAmount += (int)$item['price'] * (int)$item['quantity'] * (int)$item['numberDays'];
        }
        $cart['totalQuantity'] = $totalQuantity;
        $cart['totalAmount'] = $totalAmount + ($totalAmount * 0.1);
        $cart['totalTva'] = $totalAmount * 0.2;
        $cart['totalFees'] = $totalAmount * 0.1;

        $session->set('cart', $cart);

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
                    if ($transactionLine->getStartDate() != $transactionLine->getEndDate()) {
                        $disabledDates[] = [
                            'from' => ($transactionLine->getStartDate())->format('Y-m-d'),
                            'to' => ($transactionLine->getEndDate())->format('Y-m-d'),
                        ];
                    } else {
                        $disabledDates[] = ['from' => ($transactionLine->getStartDate())->format('Y-m-d')];
                    }
                }
            }
        }

        return $disabledDates;
    }
}
