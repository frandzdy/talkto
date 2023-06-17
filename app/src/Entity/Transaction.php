<?php

namespace App\Entity;

use App\Enum\ProductStatus;
use App\Enum\TransactionStatus;
use App\Repository\TransactionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TransactionRepository")
 */
class Transaction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column
     */
    private ?int $id = null;

    /**
     * @var Collection|ArrayCollection|null
     * @ORM\OneToMany(mappedBy="transaction", targetEntity="App\Entity\TransactionLine", cascade={"persist", "remove"})
     */
    private ?Collection $transactionLines = null;

    /**
     * @ORM\Column(type="smallint", nullable=false, enumType=TransactionStatus::class)
     */
    private ?TransactionStatus $status;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    private ?string $reference;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    private ?string $paymentIntentId;
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
     * @param string|null $reference
     */
    public function setReference(?string $reference): void
    {
        $this->reference = $reference;
    }

    /**
     * @return string|null
     */
    public function getPaymentIntentId(): ?string
    {
        return $this->paymentIntentId;
    }

    /**
     * @param string|null $paymentIntentId
     */
    public function setPaymentIntentId(?string $paymentIntentId): void
    {
        $this->paymentIntentId = $paymentIntentId;
    }
}
