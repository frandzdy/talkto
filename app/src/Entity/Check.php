<?php

namespace App\Entity;

use App\Enum\CheckStatus;
use App\Enum\ClaimStatus;
use App\Enum\ReservationStatus;
use App\Enum\TransactionLineStatus;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CheckRepository")
 */
class Check
{
    use TraitToken;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column
     */
    private ?int $id = null;

    /**
     * @ORM\OneToOne(targetEntity=TransactionLine::class)
     */
    private TransactionLine $transactionLine;

    /**
     * @var
     * @ORM\Column(type="smallint", enumType=ClaimStatus::class)
     */
    private ClaimStatus $status;

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
     * @return \DateTime|null
     */
    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    /**
     * @return mixed
     */
    public function getStatus(): CheckStatus
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus(?CheckStatus $status): self
    {
        $this->status = $status;

        return $this;
    }
}
