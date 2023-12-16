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

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\OneToMany(mappedBy: 'homePage', targetEntity: WebsiteContent::class, cascade: ['persist'])]
    #[Assert\Valid()]
    private Collection $websiteContents;

    #[ORM\ManyToMany(targetEntity: Product::class)]
    #[ORM\JoinTable(
        name: 'home_page_slider',
        joinColumns: [
            new ORM\JoinColumn(name: 'home_page_id', referencedColumnName: 'id')
        ],
        inverseJoinColumns: [
            new ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id')
        ]
    )]
    private Collection $sliders;

    #[ORM\ManyToMany(targetEntity: Product::class)]
    #[ORM\JoinTable(
        name: 'home_page_under_slider',
        joinColumns: [
            new ORM\JoinColumn(name: 'home_page_id', referencedColumnName: 'id')
        ],
        inverseJoinColumns: [
            new ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id')
        ]
    )]
    private Collection $underSliders;

    #[ORM\ManyToMany(targetEntity: Product::class)]
    #[ORM\JoinTable(
        name: 'home_page_mid',
        joinColumns: [
            new ORM\JoinColumn('home_page_id', referencedColumnName: 'id')
        ],
        inverseJoinColumns: [
            new ORM\JoinColumn('product_id', referencedColumnName: 'id')
        ]
    )]
    private Collection $mids;

    // constructor
    public function __construct() {
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
        if ($this->websiteContents->removeElement($websiteContent)) {
            // set the owning side to null (unless already changed)
            if ($websiteContent->getHomepage() === $this) {
                $websiteContent->setHomepage(null);
            }
        }

        return $this;
    }


    public function getSliders(): Collection {
        return $this->sliders;
    }

    public function addSlider(Product $slider): self {
        if (!$this->sliders->contains($slider)) {
            $this->sliders[] = $slider;
        }
        return $this;
    }

    public function removeSlider(Product $slider): self {
        $this->sliders->removeElement($slider);
        return $this;
    }

    public function getUnderSliders(): Collection {
        return $this->underSliders;
    }

    public function addUnderSlider(Product $underSlider): self {
        if (!$this->underSliders->contains($underSlider)) {
            $this->underSliders[] = $underSlider;
        }
        return $this;
    }

    public function removeUnderSlider(Product $underSlider): self {
        $this->underSliders->removeElement($underSlider);
        return $this;
    }

    public function getMids(): Collection {
        return $this->mids;
    }

    public function addMid(Product $mids): self {
        if (!$this->mids->contains($mids)) {
            $this->mids[] = $mids;
        }
        return $this;
    }

    public function removeMid(Product $mids): self {
        $this->mids->removeElement($mids);
        return $this;
    }
}
