<?php

namespace App\Entity;

use App\Enum\CheckinStatus;
use App\Enum\CheckinValidateStatus;
use App\Repository\CheckinRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CheckinRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Checkin
{
    use TraitToken, TraitAuthor;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: TransactionLine::class)]
    private TransactionLine $transactionLine;

    #[ORM\Column(type: "smallint", enumType: CheckinStatus::class)]
    private CheckinStatus $status;

    #[ORM\Column(type: "smallint", enumType: CheckinValidateStatus::class)]
    private CheckinValidateStatus $validateStatus;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\ManyToMany(targetEntity: Picture::class)]
    private Collection|ArrayCollection $pictures;

    /**
     * @var UploadedFile[]
     */
    #[Assert\All([new Assert\File(maxSize: 5242880, mimeTypes: "image/*", maxSizeMessage: "Document trop lourd.", mimeTypesMessage: "Format Image uniquement autorisé.")])]
    #[Assert\Count(max: 5, maxMessage: "5 fichiers maximums.")]
    public array $uploadedPictures = [];

    /**
     * @var string|null
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\NotBlank(message: "Information requise.")]
    private ?string $comments = null;

    #[ORM\Column]
    private \DateTime $startDate;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
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
     * @return mixed
     */
    public function getValidateStatus(): CheckinValidateStatus
    {
        return $this->validateStatus;
    }

    /**
     * @param mixed $validateStatus
     */
    public function setValidateStatus(CheckinStatus $validateStatus): self
    {
        $this->validateStatus = $validateStatus;

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
}
