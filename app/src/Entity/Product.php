<?php

namespace App\Entity;

use App\Enum\ProductCategory;
use App\Enum\ProductStatus;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Index(columns: ["status", "short_description", "title", "category"], name: "ecommerce_products")]
#[ORM\HasLifecycleCallbacks()]
class Product
{
    use TraitToken, TraitAuthor, TraitTimestamp;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var float|null
     */
    #[ORM\Column(scale: 2)]
    #[Assert\NotBlank(message: "Information requise.")]
    private ?float $amount = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Information requise.")]
    private ?string $shortDescription = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Information requise.")]
    private ?string $description = null;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\ManyToMany(targetEntity: Picture::class)]
    private Collection|ArrayCollection $pictures;


    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Review::class)]
    private Collection $reviews;

    /**
     * @var UploadedFile[]
     */
    #[Assert\All([new Assert\File(maxSize: 5242880, mimeTypes: "image/*", maxSizeMessage: "Document trop lourd.", mimeTypesMessage: "Format Image uniquement autorisé.")])]
    #[Assert\Count(max: 5, maxMessage: "5 fichiers maximums.")]
    public array $uploadedPictures = [];

    /**
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Information requise.")]
    private ?string $title = null;

    /**
     * @var ProductStatus
     */
    #[ORM\Column(type: "smallint", enumType: ProductStatus::class)]
    private ProductStatus $status;

    /**
     * @var float
     */
    #[ORM\Column(length: 11)]
    #[Assert\NotBlank(message: "Information requise.")]
    private float $caution;

    /**
     * @var int
     */
    #[ORM\Column(length: 11)]
    #[Assert\NotBlank(message: "Information requise.")]
    private int $quantity;

    /**
     * @var int|null
     */
    #[ORM\Column(length: 11)]
    private ?int $quantityAllReadyReserved = null;

    /**
     * @var ProductCategory
     */
    #[ORM\Column(type: "smallint", enumType: ProductCategory::class)]
    private ProductCategory $category;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
        $this->reviews = new ArrayCollection();
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

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

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
     * @return float
     */
    public function getCaution(): float
    {
        return $this->caution;
    }

    /**
     * @param float $caution
     */
    public function setCaution(float $caution): self
    {
        $this->caution = $caution;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * @param int|null $quantity
     */
    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getQuantityAllReadyReserved(): ?int
    {
        return $this->quantityAllReadyReserved;
    }

    /**
     * @param int|null $quantityAllReadyReserved
     * @return $this
     */
    public function setQuantityAllReadyReserved(?int $quantityAllReadyReserved): self
    {
        $this->quantityAllReadyReserved = $quantityAllReadyReserved;

        return $this;
    }

    /**
     * @return ProductCategory
     */
    public function getCategory(): ProductCategory
    {
        return $this->category;
    }

    /**
     * @param ProductCategory $category
     */
    public function setCategory(ProductCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    /**
     * @param Review $review
     * @return $this
     */

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
        }

        return $this;
    }

    /**
     * @param Review $review
     * @return $this
     */
    public function removeReview(Review $review): self
    {
        if ($this->reviews->contains($review)) {
            $this->reviews->removeElement($review);
        }

        return $this;
    }
}
