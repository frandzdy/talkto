<?php

namespace App\Entity;

use App\Form\Back\WebsiteContentType;
use App\Repository\HomePageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table()]
#[ORM\Entity(repositoryClass: HomePageRepository::class)]
class HomePage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'homePage', targetEntity: WebsiteContent::class, cascade: ['persist'])]
    #[Assert\Valid()]
    private Collection $websiteContents;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    private ?Product $sliders1 = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    private ?Product $sliders2 = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    private ?Product $sliders3 = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    private ?Product $underSliders1;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    private ?Product $underSliders2;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    private ?Product $underSliders3;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    private ?Product $mids1;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    private ?Product $mids2;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    private ?Product $mids3;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getSliders1(): ?Product
    {
        return $this->sliders1;
    }

    public function setSliders1(?Product $sliders1): void
    {
        $this->sliders1 = $sliders1;
    }

    public function getSliders2(): ?Product
    {
        return $this->sliders2;
    }

    public function setSliders2(?Product $sliders2): void
    {
        $this->sliders2 = $sliders2;
    }

    public function getSliders3(): ?Product
    {
        return $this->sliders3;
    }

    public function setSliders3(?Product $sliders3): void
    {
        $this->sliders3 = $sliders3;
    }

    public function getUnderSliders1(): ?Product
    {
        return $this->underSliders1;
    }

    public function setUnderSliders1(?Product $underSliders1): void
    {
        $this->underSliders1 = $underSliders1;
    }

    public function getUnderSliders2(): ?Product
    {
        return $this->underSliders2;
    }

    public function setUnderSliders2(?Product $underSliders2): void
    {
        $this->underSliders2 = $underSliders2;
    }

    public function getUnderSliders3(): ?Product
    {
        return $this->underSliders3;
    }

    public function setUnderSliders3(?Product $underSliders3): void
    {
        $this->underSliders3 = $underSliders3;
    }

    public function getMids1(): ?Product
    {
        return $this->mids1;
    }

    public function setMids1(?Product $mids1): void
    {
        $this->mids1 = $mids1;
    }

    public function getMids2(): ?Product
    {
        return $this->mids2;
    }

    public function setMids2(?Product $mids2): void
    {
        $this->mids2 = $mids2;
    }

    public function getMids3(): ?Product
    {
        return $this->mids3;
    }

    public function setMids3(?Product $mids3): void
    {
        $this->mids3 = $mids3;
    }

    public function __construct()
    {
        $this->websiteContents = new ArrayCollection();
    }

    /**
     * @return Collection|WebsiteContent[]
     */
    public function getWebsiteContents(): Collection
    {
        return $this->websiteContents;
    }

    public function addWebsiteContent(WebsiteContent $websiteContent): self
    {
        if (!$this->websiteContents->contains($websiteContent)) {
            $this->websiteContents[] = $websiteContent;
            $websiteContent->setHomepage($this);
        }

        return $this;
    }

    public function removeWebsiteContent(WebsiteContent $websiteContent): self
    {
        if ($this->websiteContents->removeElement($websiteContent)) {
            // set the owning side to null (unless already changed)
            if ($websiteContent->getHomepage() === $this) {
                $websiteContent->setHomepage(null);
            }
        }

        return $this;
    }
}
