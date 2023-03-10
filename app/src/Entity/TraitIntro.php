<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

trait TraitIntro
{
    /**
     * @ORM\Column(type="boolean", options={"default":0})
     */
    public $introMenu;

    /**
     */
    public function getIntroMenu()
    {
        return $this->introMenu;
    }

    /**
     */
    public function setIntroMenu($introMenu): self
    {
        $this->introMenu = $introMenu;

        return $this;
    }
}
