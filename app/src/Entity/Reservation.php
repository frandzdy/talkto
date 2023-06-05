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
         * @ORM\ManyToOne(targetEntity=Transaction::class)
         */
        private Transaction $transaction;

        private $status;
        private $reclaim;
    }
