<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table()]
#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\Index(columns: ['product_id', 'author_id', 'created_at', 'note'], name: 'ecommerce_review')]
#[ORM\HasLifecycleCallbacks]
class Review
{
    use TraitAuthor;
    use TraitTimestamp;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'reviews')]
    private Product $product;

    /**
     * Note sur 5.
     */
    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank(message: 'Information requise.')]
    private int $note;

    /**
     * Message.
     */
    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank(message: 'Information requise.')]
    private string $message;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getNote(): int
    {
        return $this->note;
    }

    public function setNote(int $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
