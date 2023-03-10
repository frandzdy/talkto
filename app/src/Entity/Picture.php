<?php
    
    namespace App\Entity;
    
    use App\Repository\PictureRepository;
    use Doctrine\ORM\Mapping as ORM;
    
    /**
     * @ORM\Entity(repositoryClass=PictureRepository::class)
     * @ORM\HasLifecycleCallbacks()
     */
    class Picture
    {
        use TraitToken;
        
        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(type="integer")
         */
        private $id;
        
        /**
         * @ORM\Column(type="string", length=255, nullable=true)
         */
        private $name;
        
        /**
         * @ORM\ManyToOne(targetEntity=User::class, inversedBy="pictures")
         */
        private $user;
        
        public function getId(): ?int
        {
            return $this->id;
        }
        
        public function getName(): ?string
        {
            return $this->name;
        }
        
        public function setName(?string $name): self
        {
            $this->name = $name;
            
            return $this;
        }
        
        public function getUser(): ?User
        {
            return $this->user;
        }
        
        public function setUser(?User $user): self
        {
            $this->user = $user;
            
            return $this;
        }
    }
