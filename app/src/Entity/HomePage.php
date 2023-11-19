<?php

namespace App\Entity;

use App\Repository\HomePageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HomePageRepository::class)]
class HomePage
{
    public function __toString()
    {
        return 'toto';
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'homePage', targetEntity: Product::class)]
    private Collection $sliders;

    public function __construct()
    {
        $this->sliders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getSliders(): Collection
    {
        return $this->sliders;
    }

    public function addSlider(Product $slide): self
    {
        if (!$this->sliders->contains($slide)) {
            $this->sliders->add($slide);
            $slide->setHomePage($this);
        }

        return $this;
    }

    public function removeSlider(Product $slide): self
    {
        if ($this->sliders->removeElement($slide)) {
            // set the owning side to null (unless already changed)
            if ($slide->getHomePage() === $this) {
                $slide->setHomePage(null);
            }
        }

        return $this;
    }
}
