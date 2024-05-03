<?php

namespace App\Entity;

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

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\OneToMany(mappedBy: 'homePage', targetEntity: WebsiteContent::class, cascade: ['persist', 'remove'])]
    #[Assert\Valid()]
    private Collection $websiteContents;

    #[ORM\OneToMany(mappedBy: 'homePage', targetEntity: Slider::class, cascade: ['persist', 'remove'])]
    #[Assert\Valid()]
    private Collection $sliders;

    #[ORM\OneToMany(mappedBy: 'homePage', targetEntity: UnderSlider::class, cascade: ['persist', 'remove'])]
    #[Assert\Valid()]
    private Collection $underSliders;

    #[ORM\OneToMany(mappedBy: 'homePage', targetEntity: Mid::class, cascade: ['persist', 'remove'])]
    #[Assert\Valid()]
    private Collection $mids;

    // constructor
    public function __construct()
    {
        $this->websiteContents = new ArrayCollection();
        $this->sliders = new ArrayCollection();
        $this->underSliders = new ArrayCollection();
        $this->mids = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
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
        // set the owning side to null (unless already changed)
        if ($this->websiteContents->removeElement($websiteContent) && $websiteContent->getHomepage() === $this) {
            $websiteContent->setHomepage(null);
        }

        return $this;
    }

    public function getSliders(): Collection
    {
        return $this->sliders;
    }

    public function addSlider(Slider $slider): self
    {
        if (!$this->sliders->contains($slider)) {
            $this->sliders[] = $slider;
            $slider->setHomePage($this);
        }

        return $this;
    }

    public function removeSlider(Slider $slider): self
    {
        if ($this->sliders->contains($slider)) {
            $this->sliders->removeElement($slider);
            if ($slider->getHomePage() === $this) {
                $slider->setHomePage($this);
            }
        }

        return $this;
    }

    public function getUnderSliders(): Collection
    {
        return $this->underSliders;
    }

    public function addUnderSlider(UnderSlider $underSlider): self
    {
        if (!$this->underSliders->contains($underSlider)) {
            $this->underSliders[] = $underSlider;
            $underSlider->setHomePage($this);
        }

        return $this;
    }

    public function removeUnderSlider(UnderSlider $underSlider): self
    {
        if ($this->underSliders->contains($underSlider)) {
            $this->underSliders->removeElement($underSlider);
            if ($underSlider->getHomePage() === $this) {
                $underSlider->setHomePage(null);
            }
        }

        return $this;
    }

    public function getMids(): Collection
    {
        return $this->mids;
    }

    public function addMid(Mid $mids): self
    {
        if (!$this->mids->contains($mids)) {
            $this->mids[] = $mids;
            $mids->setHomePage($this);
        }

        return $this;
    }

    public function removeMid(Mid $mids): self
    {
        if ($this->mids->contains($mids)) {
            $this->mids->removeElement($mids);
            if ($mids->getHomePage() === $this) {
                $mids->setHomePage(null);
            }
        }

        return $this;
    }
}
