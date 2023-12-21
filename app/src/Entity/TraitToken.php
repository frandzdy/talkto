<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Génère un token pour plus de sécurité.
 */
trait TraitToken
{
    #[ORM\Column(length: 255)]
    private ?string $token = null;

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setTokenValue(): void
    {
        if (!$this->getToken()) {
            $this->setToken(hash('sha256', random_bytes(32)));
        }
    }
}
