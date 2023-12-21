<?php

namespace App\Entity;

use App\Enum\ClaimStatus;
use App\Repository\ClaimRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table()]
#[ORM\Entity(repositoryClass: ClaimRepository::class)]
#[ORM\Index(columns: ['status', 'checkin_id'], name: 'ecommerce_claim')]
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

    #[ORM\Column(type: 'smallint', enumType: ClaimStatus::class)]
    private ClaimStatus $status;

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

    public function getStatus(): ClaimStatus
    {
        return $this->status;
    }

    public function setStatus(ClaimStatus $status): self
    {
        $this->status = $status;

        return $this;
    }
}
