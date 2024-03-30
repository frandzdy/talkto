<?php

namespace App\Entity;

use App\Enum\Link;
use App\Repository\WebsiteContentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: WebsiteContentRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class WebsiteContent
{
    use TraitAuthor;
    use TraitTimestamp;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Information requise.')]
    #[Assert\Length(max: 100, maxMessage: 'Trop long.')]
    private ?string $title = null;

    #[ORM\Column(length: 200)]
    #[Assert\NotBlank(message: 'Information requise.')]
    #[Assert\Length(max: 200, maxMessage: 'Trop long.')]
    private ?string $subTitle = null;

    #[ORM\Column(type: 'smallint', enumType: Link::class)]
    private ?Link $link = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Picture $picture = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'websiteContents')]
    private ?HomePage $homePage = null;

    #[Assert\File(
        maxSize: '10M',
        mimeTypes: ['image/svg+xml', 'image/jpg', 'image/jpeg'],
        maxSizeMessage: 'Document trop lourd. (10Mo)',
        mimeTypesMessage: 'Format image uniquement autorisé. (SVG)',
    )]
    private ?UploadedFile $uploadedPicture = null;

    #[ORM\Column(nullable: true)]
    private ?bool $whiteColor = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSubTitle(): ?string
    {
        return $this->subTitle;
    }

    public function setSubTitle(string $subTitle): self
    {
        $this->subTitle = $subTitle;

        return $this;
    }

    public function getPicture(): ?Picture
    {
        return $this->picture;
    }

    public function setPicture(Picture $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getLink(): ?Link
    {
        return $this->link;
    }

    public function setLink(?Link $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getHomePage(): ?HomePage
    {
        return $this->homePage;
    }

    public function setHomePage(?HomePage $homePage): self
    {
        $this->homePage = $homePage;

        return $this;
    }

    public function getUploadedPicture(): ?UploadedFile
    {
        return $this->uploadedPicture;
    }

    public function setUploadedPicture(?UploadedFile $uploadedPicture): void
    {
        $this->uploadedPicture = $uploadedPicture;
    }

    public function hasWhiteColor(): ?bool
    {
        return $this->whiteColor;
    }

    public function setWhiteColor(?bool $whiteColor): void
    {
        $this->whiteColor = $whiteColor;
    }
}
