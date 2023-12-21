<?php

namespace App\Entity;

use App\Enum\ReservationStatus;
use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table()]
#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\Index(columns: ['author_id', 'created_at', 'token', 'status'], name: 'ecommerce_reservation')]
#[ORM\HasLifecycleCallbacks]
class Reservation
{
    use TraitToken;
    use TraitAuthor;
    use TraitTimestamp;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Transaction::class)]
    private Transaction $transaction;

    #[ORM\Column(type: 'smallint', enumType: ReservationStatus::class)]
    private ReservationStatus $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTransaction(): Transaction
    {
        return $this->transaction;
    }

    public function setTransaction(Transaction $transaction): self
    {
        $this->transaction = $transaction;

        return $this;
    }

    public function getStatus(): ReservationStatus
    {
        return $this->status;
    }

    public function setStatus(ReservationStatus $status): self
    {
        $this->status = $status;

        return $this;
    }
}
