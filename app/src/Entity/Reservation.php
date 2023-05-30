<?php
    
    namespace App\Entity;
    
    use App\Repository\ReservationRepository;
    use Doctrine\ORM\Mapping as ORM;
    
    /**
     * @ORM\Entity(repositoryClass=ReservationRepository::class)
     * @ORM\HasLifecycleCallbacks()
     */
    class Reservation
    {
        use TraitToken;
        
        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(type="integer")
         */
        private $id;
        
        /**
         * @ORM\Column(type="datetime")
         */
        private $start;
        /**
         * @ORM\Column(type="datetime")
         */
        private $end;

        /**
         * @ORM\ManyToOne(targetEntity=Product::class)
         */
        private Product $product;
        
        public function getId(): ?int
        {
            return $this->id;
        }

        /**
         * @return mixed
         */
        public function getStart()
        {
            return $this->start;
        }

        /**
         * @param mixed $start
         */
        public function setStart($start): void
        {
            $this->start = $start;
        }

        /**
         * @return mixed
         */
        public function getEnd()
        {
            return $this->end;
        }

        /**
         * @param mixed $end
         */
        public function setEnd($end): void
        {
            $this->end = $end;
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
        public function setProduct(Product $product): void
        {
            $this->product = $product;
        }
    }
