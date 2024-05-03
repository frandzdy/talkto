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

#[ORM\Table()]
#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Index(columns: ['status', 'short_description', 'title', 'category'], name: 'ecommerce_products')]
#[ORM\HasLifecycleCallbacks()]
class Product
{
    use TraitToken;
    use TraitAuthor;
    use TraitTimestamp;
    use TraitDeletable;

    /**
     * @var UploadedFile[]
     */
    #[Assert\All(
        new Assert\Image(
            maxSize: '10M',
            mimeTypes: ['image/jpg', 'image/jpeg'],
            detectCorrupted: true,
            maxSizeMessage: 'Document trop lourd. (10Mo)',
            mimeTypesMessage: 'Format image uniquement autorisé. (JPG)',
            corruptedMessage: 'Fichier corrompue',
            groups: ['creation', 'edit']
        )
    )]
    public array $uploadedPictures = [];

    public ?string $handleError = null;

    // Uniquement pour envoyer ce message par email
    public ?string $responseRejected = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(scale: 2)]
    #[Assert\NotBlank(message: 'Information requise.', groups: ['creation', 'edit'])]
    private ?float $amount = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Information requise.', groups: ['creation', 'edit'])]
    private ?string $shortDescription = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Information requise.', groups: ['creation', 'edit'])]
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity: Picture::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $pictures;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Review::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $reviews;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Information requise.', groups: ['creation', 'edit'])]
    private ?string $title = null;

    #[ORM\Column(type: 'smallint', enumType: ProductStatus::class)]
    private ProductStatus $status;

    #[ORM\Column(length: 11)]
    #[Assert\NotBlank(message: 'Information requise.', groups: ['creation', 'edit'])]
    private float $caution;

    #[ORM\Column(length: 11)]
    #[Assert\NotBlank(message: 'Information requise.', groups: ['creation', 'edit'])]
    private int $quantity;

    #[ORM\Column(length: 11)]
    private ?int $quantityAllReadyReserved = null;

    #[ORM\Column(type: 'smallint', enumType: ProductCategory::class)]
    private ProductCategory $category;

    #[ORM\Column(type: Types::INTEGER, length: 11)]
    private ?int $numberView = 0;

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

    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
        }

        return $this;
    }

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

    public function getCaution(): float
    {
        return $this->caution;
    }

    public function setCaution(float $caution): self
    {
        $this->caution = $caution;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getQuantityAllReadyReserved(): ?int
    {
        return $this->quantityAllReadyReserved;
    }

    public function setQuantityAllReadyReserved(?int $quantityAllReadyReserved): self
    {
        $this->quantityAllReadyReserved = $quantityAllReadyReserved;

        return $this;
    }

    public function getCategory(): ProductCategory
    {
        return $this->category;
    }

    public function setCategory(ProductCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->contains($review)) {
            $this->reviews->removeElement($review);
        }

        return $this;
    }

    public function getAverageNote(): int
    {
        $totalNote = 0;
        foreach ($this->getReviews() as $review) {
            $totalNote += $review->getNote();
        }

        if ($totalNote) {
            return $totalNote / $this->getReviews()->count();
        }

        return 0;
    }

    public function getNumberView(): ?int
    {
        return $this->numberView;
    }

    public function setNumberView(?int $numberView): void
    {
        $this->numberView = $numberView;
    }
}
