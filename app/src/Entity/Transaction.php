<?php

namespace App\Entity;

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
     * @ORM\OneToMany(mappedBy="transaction", targetEntity="App\Entity\TransactionLine")
     */
    private ?Collection $transactionLines = null;

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
            $transactionLine->setProduct($this);
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
}
