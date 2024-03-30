<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Gère les champs date de création et de mise à jour.
 */
trait TraitTimestamp
{
    /**
     * Date de mise création.
     */
    #[ORM\Column(type: 'date')]
    private ?\DateTime $createdAt = null;

    /**
     * Date de mise à jour.
     */
    #[ORM\Column(type: 'date')]
    private ?\DateTime $updatedAt = null;

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateDate(): void
    {
        if (!$this->getCreatedAt()) {
            $this->setCreatedAt(new \DateTime('now'));
        }

        $this->setUpdatedAt(new \DateTime('now'));
    }
}
