<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Gère le champ de date de suppression.
 */
trait TraitDeletable
{
    /**
     * Date de suppression.
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTime $deletedAt = null;

    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}
