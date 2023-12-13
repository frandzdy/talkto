<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table()]
#[ORM\Entity()]
#[ORM\Index(columns: ["author_id", "created_at", "reservation_id"], name: "ecommerce_message")]
#[ORM\HasLifecycleCallbacks()]
class Message
{
    use TraitTimestamp;
    use TraitAuthor;
    use TraitToken;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "text")]
    private string $message;

    #[ORM\ManyToOne(targetEntity: Reservation::class)]
    private Reservation $reservation;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getCreatedAtFormatted(): string
    {
        return $this->getCreatedAt()->format('d-m-y');
    }

    /**
     * @return Reservation
     */
    public function getReservation(): Reservation
    {
        return $this->reservation;
    }

    /**
     * @param Reservation $discussion
     * @return $this
     */
    public function setReservation(Reservation $discussion): self
    {
        $this->reservation = $discussion;

        return $this;
    }
}
