<?php

namespace App\Service;

use App\Entity\Checkin;
use App\Entity\Claim;
use App\Entity\Picture;
use App\Entity\Reservation;
use App\Entity\TransactionLine;
use App\Entity\User;
use App\Enum\CheckinType;
use App\Enum\CheckinStatus;
use App\Enum\CheckinType as CheckinTypeEnum;
use App\Enum\ClaimStatus;
use App\Enum\ReservationStatus;
use App\Enum\TransactionLineStatus;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly class CheckManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FileUploadManager $fileUploadManager,
        private MailerManager $mailerManager,
        private string $emailSupport
    ) {
    }

    /**
     * Retourne un check in ou out
     */
    public function createCheckin(User $user, string $type, TransactionLine $transactionLine): Checkin
    {
        return (new Checkin())
            ->setType($type === 'in' ? CheckinType::IN : CheckinType::OUT)
            ->setTransactionLine($transactionLine)
            ->setAuthor($user);
    }

    /**
     * Sauvegarde un check in ou out
     */
    public function saveCheckin(Checkin $checkin, Reservation $reservation, array $pictureFileDatas): bool
    {
        $hasClaim = false;
        if ($pictureFileDatas) {
            foreach ($pictureFileDatas as $pictureFileData) {
                if ($pictureFileData instanceof UploadedFile) {
                    $fileName = $this->fileUploadManager->uploadFile('checkin', $pictureFileData);
                    $pic = (new Picture())
                        ->setName($fileName);
                    $this->entityManager->persist($pic);
                    $checkin->addPicture($pic);
                }
            }
        }

        // si on a le type check in ou checkOut alors, on indique qu'on a démarré la transactionLine
        if ($checkin->getType() === CheckinType::IN) {
            $checkin->getTransactionLine()->setStatus(TransactionLineStatus::IN_PROGRESS);
        } else {
            $hasCheckinValidate = $checkin->getTransactionLine()->getCheckins()->filter(
                fn(Checkin $checkin) => $checkin->getStatus() === CheckinStatus::VALIDATE
            )->count();
            // s'il y a une réclamation lors de la fermeture de la réservation du produit
            if ($checkin->getStatus() === CheckinStatus::VALIDATE_WITH_WARNING && $hasCheckinValidate) {
                // créer une réclamation pour le back office
                $claim = (new Claim())
                    ->setCheckin($checkin)
                    ->setStatus(ClaimStatus::PENDING);
                $checkin->addClaim($claim);
                $hasClaim = true;
                $this->mailerManager->sendMailNotification(
                    $this->emailSupport,
                    'emails/admin_claim.html.twig',
                    [
                        'claim' => $claim
                    ]
                );
            }
            $checkin->getTransactionLine()->setStatus(TransactionLineStatus::FINISHED);
        }

        $nbTransactionLine = $reservation->getTransaction()->getTransactionLines()->count();
        $nbTransactionLineValidated = 0;
        foreach ($reservation->getTransaction()->getTransactionLines() as $transactionLine) {
            /**
             * @var TransactionLine $transactionLine
             */
            if ($transactionLine->getStatus() === TransactionLineStatus::FINISHED) {
                $nbTransactionLineValidated += 1;
            }
        }
        // si on est sur le checkOut
        if ($checkin->getType() === CheckinType::OUT) {
            // si on a le nb de transaction validé et le mm que le nombre de réservations alors, on clôture la réservation
            if ($nbTransactionLineValidated === $nbTransactionLine) {
                $reservation->setStatus(ReservationStatus::FINISHED);
            }
        }
        // notif client
        $this->mailerManager->sendMailNotification(
            $checkin->getTransactionLine()->getTransaction()->getAuthor()->getEmail(),
            'emails/checkin.html.twig',
            [
                'user' => $checkin->getTransactionLine()->getTransaction()->getAuthor(),
                'checkin' => $checkin
            ]
        );
        // notif le bailleur
        $this->mailerManager->sendMailNotification(
            $checkin->getTransactionLine()->getProduct()->getAuthor()->getEmail(),
            'emails/checkin.html.twig',
            [
                'user' => $checkin->getTransactionLine()->getProduct()->getAuthor(),
                'checkin' => $checkin
            ]
        );

        if (!$checkin->getId()) {
            $checkin->setStartDate(new \DateTime());
            $this->entityManager->persist($checkin);
        }

        $this->entityManager->flush();

        return $hasClaim;
    }
}
