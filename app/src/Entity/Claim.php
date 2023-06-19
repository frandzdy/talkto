<?php

namespace App\Entity;

use App\Enum\ReservationStatus;
use App\Enum\TransactionLineStatus;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClaimRepository")
 */
class Claim
{
    use TraitToken;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column
     */
    private ?int $id = null;

    /**
     *
     * @ORM\Column(type="integer", length="11")
     */
    private ?int $quantity = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Transaction", inversedBy="transactionLines")
     */
    private Transaction $transaction;

    /**
     * @var
     * @ORM\Column(type="smallint", enumType=TransactionLineStatus::class)
     */
    private TransactionLineStatus $status;

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
     * @return Transaction
     */
    public function getTransaction(): Transaction
    {
        return $this->transaction;
    }

    /**
     * @param Transaction $transaction
     */
    public function setTransaction(Transaction $transaction): self
    {
        $this->transaction = $transaction;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime|null $startDate
     */
    public function setStartDate(?\DateTime $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime|null $endDate
     */
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

    /**
     * Check si on peut annuler une réservation
     * @return bool
     */
    public function canBeCancel(): bool
    {
        return !in_array($this->getStatus()->value, [TransactionLineStatus::CANCELED->value, TransactionLineStatus::FINISHED->value])
            && (new \DateTime('now') < $this->getStartDate());
    }
}
