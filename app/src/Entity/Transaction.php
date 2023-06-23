<?php

namespace App\Entity;

use App\Enum\ProductStatus;
use App\Enum\TransactionStatus;
use App\Repository\TransactionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Index(columns: ["author_id", "created_at", "token", "status"], name: "ecommerce_transaction")]
#[ORM\HasLifecycleCallbacks()]
class Transaction
{
    use TraitToken, TraitAuthor, TraitTimestamp;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection|ArrayCollection
     */
    #[ORM\OneToMany(mappedBy: "transaction", targetEntity: TransactionLine::class, cascade: ["persist", "remove"])]
    private Collection $transactionLines;

    /**
     * @var TransactionStatus
     */
    #[ORM\Column(type: "smallint", enumType: TransactionStatus::class)]
    private TransactionStatus $status;

    /**
     * @var string
     */
    #[ORM\Column(length: 20)]
    private string $reference;

    /**
     * @var string
     */
    #[ORM\Column(length: 40)]
    private string $paymentIntentId;

    /**
     * @var int
     */
    #[ORM\Column(length: 11)]
    private int $totalAmountTtc;

    /**
     * @var int
     */
    #[ORM\Column(length: 11)]
    private int $totalAmountTva;

    /**
     * @var int
     */
    #[ORM\Column(length: 11)]
    private int $totalFees;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->transactionLines = new ArrayCollection();
    }

    public function getTransactionLines(): ?Collection
    {
        return $this->transactionLines;
    }

    public function addTransactionLine(TransactionLine $transactionLine): self
    {
        if (!$this->transactionLines->contains($transactionLine)) {

            $this->transactionLines[] = $transactionLine;
            $transactionLine->setTransaction($this);
        }

        return $this;
    }

    public function removeTransactionLine(TransactionLine $transactionLine): self
    {
        if ($this->transactionLines->removeElement($transactionLine)) {

            if ($transactionLine->getTransaction() === $this) {
                $transactionLine->setTransaction(null);
            }
        }

        return $this;
    }

    /**
     * @return TransactionStatus
     */
    public function getStatus(): TransactionStatus
    {
        return $this->status;
    }

    /**
     * @param TransactionStatus $status
     */
    public function setStatus(TransactionStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getReference(): ?string
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     */
    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }

    /**
     * @return string
     */
    public function getPaymentIntentId(): string
    {
        return $this->paymentIntentId;
    }

    /**
     * @param string $paymentIntentId
     */
    public function setPaymentIntentId(string $paymentIntentId): self
    {
        $this->paymentIntentId = $paymentIntentId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTotalAmountTtc(): int
    {
        return $this->totalAmountTtc;
    }

    /**
     * @param int $totalAmountTtc
     */
    public function setTotalAmountTtc(int $totalAmountTtc): self
    {
        $this->totalAmountTtc = $totalAmountTtc;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotalAmountTva(): int
    {
        return $this->totalAmountTva;
    }

    /**
     * @param int $totalAmountTva
     */
    public function setTotalAmountTva(int $totalAmountTva): self
    {
        $this->totalAmountTva = $totalAmountTva;

        return $this;
    }


    /**
     * @return int
     */
    public function getTotalFees(): int
    {
        return $this->totalFees;
    }

    /**
     * @param int $totalFees
     */
    public function setTotalFees(int $totalFees): self
    {
        $this->totalFees = $totalFees;

        return $this;
    }
}
