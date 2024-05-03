<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table()]
#[ORM\Entity()]
#[UniqueEntity(fields: ['product'], message: 'Produit déjà enregistré.')]
class Mid
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(unique: true)]
    #[Assert\NotBlank(message: 'Information requise.')]
    private ?Product $product = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'mids')]
    private ?HomePage $homePage;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): void
    {
        $this->product = $product;
    }

    public function getHomePage(): ?HomePage
    {
        return $this->homePage;
    }

    public function setHomePage(?HomePage $homePage): void
    {
        $this->homePage = $homePage;
    }
}
