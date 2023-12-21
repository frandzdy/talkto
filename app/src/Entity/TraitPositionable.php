<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

trait TraitPositionable
{
    #[ORM\Column(type: 'smallint')]
    private int $position;

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }
}
