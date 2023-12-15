<?php

namespace App\Entity;

use App\Enum\CheckinType;
use App\Enum\TransactionLineStatus;
use App\Repository\TransactionLineRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table()]
#[ORM\Entity(repositoryClass: TransactionLineRepository::class)]
#[ORM\Index(columns: ["start_date", "end_date", "token", "status"], name: "ecommerce_transaction_line")]
#[ORM\HasLifecycleCallbacks()]
class TransactionLine
{
    use TraitToken;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var int
     */
    #[ORM\Column(type: "integer", length: 11)]
    private int $quantity;

    /**
     * @var \DateTime
     */
    #[ORM\Column(type: "date")]
    #[Assert\DateTime()]
    private \DateTime $startDate;

    /**
     * @var \DateTime
     */
    #[ORM\Column()]
    #[Assert\DateTime()]
    private \DateTime $endDate;

    /**
     * @var Product
     */
    #[ORM\ManyToOne()]
    private Product $product;

    /**
     * @var int
     */
    #[ORM\Column(length: 11)]
    private int $amountTtc;

    /**
     * @var int
     */
    #[ORM\Column(length: 11)]
    private int $amountTva;

    /**
     * @var int
     */
    #[ORM\Column(length: 11)]
    private int $fees;

    /**
     * @var Transaction
     */
    #[ORM\ManyToOne(inversedBy: "transactionLines")]
    private ?Transaction $transaction;

    /**
     * @var TransactionLineStatus
     */
    #[ORM\Column(type: "smallint", enumType: TransactionLineStatus::class)]
    private TransactionLineStatus $status;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $transfertId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cancelTransfertId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cautionId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $captureCautionId = null;

    #[ORM\Column(length: 11, nullable: true)]
    private ?int $cancelAmount = null;

    #[ORM\Column(length: 11, nullable: true)]
    private ?string $cautionAmount = null;

    #[ORM\OneToMany(mappedBy: 'transactionLine', targetEntity: Checkin::class, cascade: [
        'remove',
        'persist'
    ], orphanRemoval: true)]
    private Collection $checkins;

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return Transaction|null
     */
    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    /**
     * @param Transaction|null $transaction
     * @return $this
     */
    public function setTransaction(?Transaction $transaction): self
    {
        $this->transaction = $transaction;

        return $this;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTime $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTime $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus(): TransactionLineStatus
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus(?TransactionLineStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAmountTtc(): ?int
    {
        return $this->amountTtc;
    }

    /**
     * @param int|null $amountTtc
     */
    public function setAmountTtc(?int $amountTtc): self
    {
        $this->amountTtc = $amountTtc;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAmountTva(): ?int
    {
        return $this->amountTva;
    }

    /**
     * @param int|null $amountTva
     */
    public function setAmountTva(?int $amountTva): self
    {
        $this->amountTva = $amountTva;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFees(): ?int
    {
        return $this->fees;
    }

    /**
     * @param int|null $fees
     */
    public function setFees(?int $fees): self
    {
        $this->fees = $fees;

        return $this;
    }

    public function getTransfertId(): ?string
    {
        return $this->transfertId;
    }

    public function setTransfertId(?string $transfertId): self
    {
        $this->transfertId = $transfertId;

        return $this;
    }

    public function getCancelTransfertId(): ?string
    {
        return $this->cancelTransfertId;
    }

    public function setCancelTransfertId(?string $cancelTransfertId): self
    {
        $this->cancelTransfertId = $cancelTransfertId;

        return $this;
    }

    /**
     * Check si on peut annuler une réservation
     *
     * @return bool
     */
    public function canBeCancel(): bool
    {
        return $this->getStatus()->value == TransactionLineStatus::WAITING->value
            && $this->getStartDate() > (new \DateTime('now'));
    }

    /**
     * @return Collection
     */
    public function getCheckins(): Collection
    {
        return $this->checkins;
    }

    /**
     * @param Checkin $checkin
     * @return $this
     */

    public function addCheckIn(Checkin $checkin): self
    {
        if (!$this->checkins->contains($checkin)) {
            $this->checkins[] = $checkin;
            $checkin->setTransactionLine($this);
        }

        return $this;
    }

    /**
     * @param Checkin $checkin
     * @return $this
     */
    public function removeCheckin(Checkin $checkin): self
    {
        if ($this->checkins->contains($checkin)) {
            $this->checkins->removeElement($checkin);
            if ($this === $checkin->getTransactionLine()) {
                $checkin->setTransactionLine(null);
            }
        }

        return $this;
    }

    /*
     * Retourne le checkin selon le type choisi
     */
    public function getCheck(CheckinType $checkinStatus): ?Collection
    {
        return $this->getCheckins()->filter(
            function (Checkin $checkin) use ($checkinStatus) {
                return $checkin->getType() === $checkinStatus;
            }
        );
    }

    public function getCancelAmount(): ?int
    {
        return $this->cancelAmount;
    }

    public function setCancelAmount(?int $cancelAmount): self
    {
        $this->cancelAmount = $cancelAmount;

        return $this;
    }

    public function getCautionId(): ?string
    {
        return $this->cautionId;
    }

    public function setCautionId(?string $cautionId): self
    {
        $this->cautionId = $cautionId;

        return $this;
    }

    public function getCautionAmount(): ?string
    {
        return $this->cautionAmount;
    }

    public function setCautionAmount(?string $cautionAmount): self
    {
        $this->cautionAmount = $cautionAmount;

        return $this;
    }

    public function getCaptureCautionId(): ?string
    {
        return $this->captureCautionId;
    }

    public function setCaptureCautionId(?string $captureCautionId): self
    {
        $this->captureCautionId = $captureCautionId;

        return $this;
    }
}
