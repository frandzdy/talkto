<?php

namespace App\Entity;

use App\Enum\TransactionStatus;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table()]
#[ORM\Entity()]
#[ORM\Index(columns: ['author_id', 'created_at', 'token', 'status'], name: 'ecommerce_transaction')]
#[ORM\HasLifecycleCallbacks()]
class Transaction
{
    use TraitToken;
    use TraitAuthor;
    use TraitTimestamp;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'transaction', targetEntity: TransactionLine::class, cascade: ['persist', 'remove'])]
    private Collection $transactionLines;

    #[ORM\Column(type: 'smallint', enumType: TransactionStatus::class)]
    private TransactionStatus $status;

    /**
     * Référence de la transaction.
     */
    #[ORM\Column(length: 20, nullable: true)]
    private ?string $reference = null;

    /**
     * Identifiant de paiement.
     */
    #[ORM\Column(length: 40, nullable: true)]
    private ?string $paymentIntentId = null;

    /**
     * Montant total TTC.
     */
    #[ORM\Column(length: 11)]
    private int $totalAmountTtc;

    /**
     * Montant total TVA.
     */
    #[ORM\Column(length: 11)]
    private int $totalAmountTva;

    /**
     * Montant total des frais.
     */
    #[ORM\Column(length: 11)]
    private int $totalFees;

    public function __construct()
    {
        $this->transactionLines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
        if ($this->transactionLines->removeElement($transactionLine) && $transactionLine->getTransaction() === $this) {
            $transactionLine->setTransaction(null);
        }

        return $this;
    }

    public function getStatus(): TransactionStatus
    {
        return $this->status;
    }

    public function setStatus(TransactionStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }

    public function getPaymentIntentId(): string
    {
        return $this->paymentIntentId;
    }

    public function setPaymentIntentId(string $paymentIntentId): self
    {
        $this->paymentIntentId = $paymentIntentId;

        return $this;
    }

    public function getTotalAmountTtc(): int
    {
        return $this->totalAmountTtc;
    }

    public function setTotalAmountTtc(int $totalAmountTtc): self
    {
        $this->totalAmountTtc = $totalAmountTtc;

        return $this;
    }

    public function getTotalAmountTva(): int
    {
        return $this->totalAmountTva;
    }

    public function setTotalAmountTva(int $totalAmountTva): self
    {
        $this->totalAmountTva = $totalAmountTva;

        return $this;
    }

    public function getTotalFees(): int
    {
        return $this->totalFees;
    }

    public function setTotalFees(int $totalFees): self
    {
        $this->totalFees = $totalFees;

        return $this;
    }
}
