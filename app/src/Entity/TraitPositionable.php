<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

trait TraitPositionable
{
    /**
     * @var int
     */
    #[ORM\Column(type: "smallint")]
    private int $position;

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     * @return TraitPositionable|Country
     */
    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }
}
