<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

trait TraitAuthor
{
    /**
     * @var
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "author_id", referencedColumnName: "id")]
    private User $author;

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param User $author
     * @return Reservation|Message|Product|TraitAuthor|Transaction
     */
    public function setAuthor(User $author): self
    {
        $this->author = $author;

        return $this;
    }
}
