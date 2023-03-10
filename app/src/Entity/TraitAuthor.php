<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

trait TraitAuthor
{
    /**
     * @var
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="auteur_id", referencedColumnName="id", nullable=true)
     */
    private $author;

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param $author
     * @return $this
     */
    public function setAuthor($author): self
    {
        $this->author = $author;

        return $this;
    }
}
