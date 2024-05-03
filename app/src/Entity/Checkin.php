<?php

namespace App\Entity;

use App\Enum\CheckinStatus;
use App\Enum\CheckinType;
use App\Repository\CheckinRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table()]
#[ORM\Entity(repositoryClass: CheckinRepository::class)]
#[ORM\Index(columns: ['status', 'type', 'start_date'], name: 'ecommerce_checkin')]
class Checkin
{
    use TraitToken;
    use TraitAuthor;

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
            corruptedMessage: 'Fichier corrompue.'
        )
    )]
    public array $uploadedPictures = [];

    public ?string $handleError = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: TransactionLine::class, inversedBy: 'checkins')]
    private TransactionLine $transactionLine;

    #[ORM\Column(type: 'smallint', enumType: CheckinType::class)]
    private CheckinType $type;

    #[ORM\Column(type: 'smallint', enumType: CheckinStatus::class)]
    private CheckinStatus $status = CheckinStatus::VALIDATE;

    #[ORM\ManyToMany(targetEntity: Picture::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $pictures;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comments = null;

    #[ORM\Column(type: 'date')]
    private \DateTime $startDate;

    #[ORM\OneToMany(mappedBy: 'checkin', targetEntity: Claim::class, cascade: [
        'persist',
        'remove',
    ], orphanRemoval: true)]
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

    public function getTransactionLine(): TransactionLine
    {
        return $this->transactionLine;
    }

    public function setTransactionLine(TransactionLine $transactionLine): self
    {
        $this->transactionLine = $transactionLine;

        return $this;
    }

    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getType(): CheckinType
    {
        return $this->type;
    }

    public function setType(CheckinType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getStatus(): CheckinStatus
    {
        return $this->status;
    }

    public function setStatus(CheckinStatus $status): self
    {
        $this->status = $status;

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

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(?string $comments): self
    {
        $this->comments = $comments;

        return $this;
    }

    public function getClaims(): Collection
    {
        return $this->claims;
    }

    public function addClaim(Claim $claim): self
    {
        if (!$this->claims->contains($claim)) {
            $this->claims[] = $claim;
            $claim->setCheckin($this);
        }

        return $this;
    }

    public function removeClaim(Claim $claim): self
    {
        if ($this->claims->removeElement($claim) && $this === $claim->getCheckin()) {
            $claim->setCheckin(null);
        }

        return $this;
    }
}
