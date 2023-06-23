<?php

namespace App\Entity;

use App\Enum\ReservationStatus;
use App\Enum\TransactionLineStatus;
use App\Repository\CheckRepository;
use App\Repository\ClaimRepository;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClaimRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Claim
{
    use TraitToken;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
