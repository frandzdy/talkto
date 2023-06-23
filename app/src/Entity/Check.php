<?php

namespace App\Entity;

use App\Enum\CheckStatus;
use App\Enum\ClaimStatus;
use App\Enum\ReservationStatus;
use App\Enum\TransactionLineStatus;
use App\Repository\CheckRepository;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CheckRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Check
{
    use TraitToken;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: TransactionLine::class)]
    private TransactionLine $transactionLine;

    #[ORM\Column(type: "smallint", enumType: CheckStatus::class)]
    private CheckStatus $status;

    #[ORM\Column]
    private \DateTime $startDate;

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
    public function getStatus(): CheckStatus
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus(CheckStatus $status): self
    {
        $this->status = $status;

        return $this;
    }
}
