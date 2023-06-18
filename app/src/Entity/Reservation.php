<?php
    
    namespace App\Entity;
    
    use App\Enum\ReservationStatus;
    use App\Repository\ReservationRepository;
    use Doctrine\ORM\Mapping as ORM;
    
    /**
     * @ORM\Entity(repositoryClass=ReservationRepository::class)
     * @ORM\Table(indexes={
     *     @ORM\Index(name="ecommerce_reservation", columns={"author_id", "created_at", "token", "status"})
     * })
     * @ORM\HasLifecycleCallbacks()
     */
    class Reservation
    {
        use TraitToken, TraitAuthor, TraitTimestamp;

        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(type="integer")
         */
        private $id;
        
        /**
         * @ORM\ManyToOne(targetEntity=Transaction::class)
         */
        private Transaction $transaction;

        private $reclaim;

        /**
         * @var
         * @ORM\Column(type="smallint", enumType=ReservationStatus::class)
         */
        private ReservationStatus $status;

        /**
         * @return mixed
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * @return Transaction
         */
        public function getTransaction(): Transaction
        {
            return $this->transaction;
        }

        /**
         * @param Transaction $transaction
         */
        public function setTransaction(Transaction $transaction): self
        {
            $this->transaction = $transaction;

            return $this;
        }

        /**
         * @return mixed
         */
        public function getStatus(): ReservationStatus
        {
            return $this->status;
        }

        /**
         * @param mixed $status
         */
        public function setStatus(ReservationStatus $status): self
        {
            $this->status = $status;

            return $this;
        }

        /**
         * @return mixed
         */
        public function getReclaim()
        {
            return $this->reclaim;
        }

        /**
         * @param mixed $reclaim
         */
        public function setReclaim($reclaim): self
        {
            $this->reclaim = $reclaim;

            return $this;
        }
    }
