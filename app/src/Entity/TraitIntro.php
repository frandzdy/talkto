<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

trait TraitIntro
{
    /**
     * @ORM\Column(type="boolean", options={"default":0})
     */
    public bool $introMenu = false;

    /**
     * @return bool|int
     */
    public function getIntroMenu(): bool|int
    {
        return $this->introMenu;
    }

    /**
     * @param int $introMenu
     * @return User|TraitIntro
     */
    public function setIntroMenu(int $introMenu): self
    {
        $this->introMenu = $introMenu;

        return $this;
    }
}
