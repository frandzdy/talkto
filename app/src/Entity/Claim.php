<?php

namespace App\Entity;

use App\Enum\ReservationStatus;
use App\Enum\TransactionLineStatus;
use App\Repository\CheckinRepository;
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

    #[ORM\ManyToOne(targetEntity: Checkin::class, inversedBy: 'claims')]
    private ?Checkin $checkin = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCheckin(): ?Checkin
    {
        return $this->checkin;
    }

    public function setCheckin(?Checkin $checkin): self
    {
        $this->checkin = $checkin;

        return $this;
    }
}
