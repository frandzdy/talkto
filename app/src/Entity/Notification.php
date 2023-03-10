<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
 */
class Notification
{
    use TraitTimestamp;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="notifications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Wall", inversedBy="notifications")
     * @ORM\JoinColumn(nullable=true)
     */
//    private $wall;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Message", inversedBy="notifications")
     * @ORM\JoinColumn(nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isRead;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return $this
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

//    /**
//     * @return Wall|null
//     */
//    public function getWall(): ?Wall
//    {
//        return $this->wall;
//    }
//
//    /**
//     * @param Wall|null $wall
//     * @return $this
//     */
//    public function setWall(?Wall $wall): self
//    {
//        $this->wall = $wall;
//
//        return $this;
//    }

    public function isRead(): ?bool
    {
        return $this->isRead;
    }

    public function setRead(bool $isRead): self
    {
        $this->isRead = $isRead;

        return $this;
    }

    /**
     * @return Message|null
     */
    public function getMessage(): ?Message
    {
        return $this->message;
    }

    /**
     * @param Message|null $message
     * @return $this
     */
    public function setMessage(?Message $message): self
    {
        $this->message = $message;

        return $this;
    }
}
