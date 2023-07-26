<?php

namespace App\Service;

use App\Entity\Checkin;
use App\Entity\Picture;
use App\Entity\TransactionLine;
use App\Entity\User;
use App\Enum\CheckinStatus;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CheckManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FileUploadManager $fileUploadManager
    ) {
    }

    /**
     * Retourne un check in ou out
     */
    public function createCheckin(User $user, string $type, TransactionLine $transactionLine): Checkin
    {
        return (new Checkin())
            ->setStatus($type === 'in' ? CheckinStatus::IN : CheckinStatus::OUT)
            ->setTransactionLine($transactionLine)
            ->setAuthor($user)
        ;
    }

    /**
     * Sauvegarde un check in ou out
     */
    public function saveCheckin(Checkin $checkin, array $pictureFileDatas): bool
    {
        if ($pictureFileDatas) {
            foreach ($pictureFileDatas as $pictureFileData) {
                if ($pictureFileData instanceof UploadedFile) {
                    $fileName = $this->fileUploadManager->uploadFile('check'. $checkin->getStatus() == CheckinStatus::IN ? 'in' : 'out', $pictureFileData);
                    $pic = (new Picture())
                        ->setName($fileName);
                    $this->entityManager->persist($pic);
                    $checkin->addPicture($pic);
                }
            }
        }

        if (!$checkin->getId()) {
            $checkin->setStartDate(new \DateTime());
            $this->entityManager->persist($checkin);
        }

        $this->entityManager->flush();
        
        return true;
    }
}
