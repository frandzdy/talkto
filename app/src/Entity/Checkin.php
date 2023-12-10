<?php

namespace App\Entity;

use App\Enum\CheckinType;
use App\Enum\CheckinStatus;
use App\Repository\CheckinRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table()]
#[ORM\Entity(repositoryClass: CheckinRepository::class)]
#[ORM\Index(columns: ["status", "type", "start_date"], name: "ecommerce_checkin")]
class Checkin
{
    use TraitToken, TraitAuthor;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: TransactionLine::class, inversedBy: 'checkins')]
    private TransactionLine $transactionLine;

    #[ORM\Column(type: "smallint", enumType: CheckinType::class)]
    private CheckinType $type;

    #[ORM\Column(type: "smallint", enumType: CheckinStatus::class)]
    private CheckinStatus $status = CheckinStatus::VALIDATE;

    /**
     * @var Collection
     */
    #[ORM\ManyToMany(targetEntity: Picture::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $pictures;

    /**
     * @var UploadedFile[]
     */
    #[Assert\All(
        new Assert\Image(
            maxSize: '10M',
            detectCorrupted: true,
            maxSizeMessage: "Document trop lourd.",
            mimeTypesMessage: "Format Image uniquement autorisé.",
            corruptedMessage: 'Fichier corrompue.'
        )
    )]
    public array $uploadedPictures = [];

    /**
     * @var string|null
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\NotBlank(message: 'Information requise.')]
    private ?string $comments = null;

    #[ORM\Column]
    private \DateTime $startDate;

    #[ORM\OneToMany(mappedBy: 'checkin', targetEntity: Claim::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $claims;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
        $this->claims = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return TransactionLine
     */
    public function getTransactionLine(): TransactionLine
    {
        return $this->transactionLine;
    }

    /**
     * @param TransactionLine $transactionLine
     */
    public function setTransactionLine(TransactionLine $transactionLine): self
    {
        $this->transactionLine = $transactionLine;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     * @return $this
     */
    public function setStartDate(\DateTime $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getType(): CheckinType
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType(CheckinType $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus(): CheckinStatus
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus(CheckinStatus $status): self
    {
        $this->status = $status;

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

    /**
     * @return string|null
     */
    public function getComments(): ?string
    {
        return $this->comments;
    }

    /**
     * @param string|null $comments
     */
    public function setComments(?string $comments): self
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getClaims(): Collection
    {
        return $this->claims;
    }

    /**
     * @param Claim $claim
     * @return $this
     */

    public function addClaim(Claim $claim): self
    {
        if (!$this->claims->contains($claim)) {
            $this->claims[] = $claim;
            $claim->setCheckin($this);
        }

        return $this;
    }

    /**
     * @param Claim $claim
     * @return $this
     */
    public function removeClaim(Claim $claim): self
    {
        if ($this->claims->removeElement($claim)) {
            if ($this === $claim->getCheckin()) {
                $claim->setCheckin(null);
            }
        }

        return $this;
    }
}
