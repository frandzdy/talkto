<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

trait TraitAuthor
{
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'author_id', referencedColumnName: 'id')]
    private User $author;

    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @return Message|Product|Reservation|TraitAuthor|Transaction
     */
    public function setAuthor(User $author): self
    {
        $this->author = $author;

        return $this;
    }
}
