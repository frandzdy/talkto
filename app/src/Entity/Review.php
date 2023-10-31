<?php

    namespace App\Entity;

    use App\Enum\ReservationStatus;
    use App\Repository\ReservationRepository;
    use Doctrine\DBAL\Types\Types;
    use Doctrine\ORM\Mapping as ORM;

    #[ORM\Entity(repositoryClass: ReservationRepository::class)]
    #[ORM\Index(columns: ["author_id", "created_at"], name: "ecommerce_review")]
    #[ORM\HasLifecycleCallbacks]
    class Review
    {
        use TraitAuthor, TraitTimestamp;

        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column]
        private ?int $id = null;

        /**
         * @var Product
         */
        #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'reviews')]
        private Product $product;

        /**
         * Note sur 5
         */
        #[ORM\Column(type:Types::INTEGER)]
        private int $note;

        /**
         * Message
         */
        #[ORM\Column(type:Types::STRING)]
        private string $message;

        /**
         * @return mixed
         */
        public function getId()
        {
            return $this->id;
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
         * @return mixed
         */
        public function getNote(): int
        {
            return $this->note;
        }

        /**
         * @param int $note
         */
        public function setNote(int $note): self
        {
            $this->note = $note;

            return $this;
        }

        /**
         * @return string
         */
        public function getMessage(): string
        {
            return $this->message;
        }

        /**
         * @param string $message
         */
        public function setMessage(string $message): self
        {
            $this->message = $message;

            return $this;
        }
    }
