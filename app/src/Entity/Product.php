<?php

namespace App\Entity;

use App\Enum\ProductStatus;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @ORM\Table(indexes={
 *     @ORM\Index(name="ecommerce_products", columns={"status", "description", "title"})
 * })
 */
class Product
{
    use TraitToken;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="float", scale=2)
     * @Assert\NotBlank(message="Information requise.")
     *
     */
    private ?float $amount = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Information requise.")
     */
    private ?string $description = null;

    /**
     * @ORM\ManyToMany(targetEntity=Picture::class)
     */
    private Collection $pictures;

    /**
     * @var UploadedFile[]
     * @Assert\All(
     *     {@Assert\File(maxSize=5242880, mimeTypes="image/*", maxSizeMessage="Document trop lourd.", mimeTypesMessage="Format Image uniquement autorisé.")}
     * )
     * @Assert\Count(max=5, maxMessage="5 fichiers maximums.")
     */
    public array $uploadedPictures = [];

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Information requise.")
     */
    private ?string $title = null;

    /**
     * @ORM\Column(type="smallint", nullable=false, enumType=ProductStatus::class)
     */
    private ?ProductStatus $status;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private ?User $user = null;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    /**
     * @param Picture $picture
     * @return $this
     */

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
        }

        return $this;
    }

    /**
     * @param Picture $picture
     * @return $this
     */
    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->contains($picture)) {
            $this->pictures->removeElement($picture);
        }

        return $this;
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

    public function getStatus(): ?ProductStatus
    {
        return $this->status;
    }

    public function setStatus(ProductStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return $this
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
